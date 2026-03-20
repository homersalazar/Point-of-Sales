@extends('layouts.app')

@section('content')
<div class="flex flex-col w-full space-y-6 p-4 sm:p-5">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">

        <!-- Left Side -->
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-base-content">
                Sales Order
            </h1>
            <p class="text-xs sm:text-sm text-base-content/50 mt-0.5">
                {{ \Carbon\Carbon::now()->format('F d, Y') }}
            </p>
        </div>

        <!-- Right Side (View Toggle) -->
        <div class="flex items-center gap-3 w-full md:w-auto mt-2 md:mt-0">
            <div class="join w-full sm:w-32">
                <input class="join-item btn view-toggle"
                    type="radio"
                    name="view"
                    value="grid"
                    checked
                    aria-label="Grid" />
                <input class="join-item btn view-toggle"
                    type="radio"
                    name="view"
                    value="table"
                    aria-label="Table" />
            </div>
        </div>

    </div>

    <!-- Divider -->
    <div class="divider my-0"></div>

    {{-- Filter and Viewer --}}
    <div class="flex flex-col md:flex-row md:items-center gap-2 overflow-x-auto">
        <!-- Status Buttons -->
        <div class="gridView flex gap-2 mb-5 md:mb-0 overflow-x-auto">
            @foreach($status as $stats)
                <button
                    onclick="setStatus(this, '{{ strtolower($stats->name) }}')"
                    data-stats="{{ strtolower($stats->name) }}"
                    class="stats-tab btn btn-ghost flex-shrink-0 w-28 sm:w-32 h-auto py-2.5 px-4 flex-col items-start rounded-xl border border-base-300 bg-white normal-case hover:bg-yellow-50 hover:border-yellow-400 transition-all {{ $stats->id === 0 ? '!bg-yellow-500 !border-yellow-500 text-white hover:!bg-yellow-600' : '' }}">
                    <span class="font-bold text-sm {{ $stats->id === 0 ? 'text-white' : 'text-base-content' }}">{{ $stats->name }}</span>
                    <span class="text-xs font-normal mt-0.5 {{ $stats->id === 0 ? 'text-white/70' : 'text-base-content/50' }}">{{ $stats->total_products }}</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Content --}}
    <div class="gridView grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @include('sale.partials.order_card')
    </div>

    <div class="tableView hidden bg-base-100 border border-base-200 rounded-2xl shadow-sm p-4 sm:p-6 overflow-x-auto">
        @include('sale.partials.order_table')
    </div>

</div>

<script>
    // View toggle
    document.querySelectorAll('.view-toggle').forEach(radio => {
        radio.addEventListener('change', function () {
            document.querySelectorAll('.gridView').forEach(el => {
                el.classList.toggle('hidden', this.value !== 'grid');
            });
            document.querySelectorAll('.tableView').forEach(el => {
                el.classList.toggle('hidden', this.value !== 'table');
            });
        });
    });

    // Status filter
    const setStatus = (el, stats) => {
        document.querySelectorAll('.stats-tab').forEach(t => {
            t.classList.remove('!bg-yellow-500', '!border-yellow-500');
            t.querySelectorAll('span').forEach((s, i) => {
                s.classList.remove('text-white', 'text-white/70');
                s.classList.add(i === 0 ? 'text-base-content' : 'text-base-content/50');
            });
        });

        el.classList.add('!bg-yellow-500', '!border-yellow-500');
        el.querySelectorAll('span').forEach((s, i) => {
            s.classList.remove('text-base-content', 'text-base-content/50');
            s.classList.add(i === 0 ? 'text-white' : 'text-white/70');
        });

        document.querySelectorAll('.order-card').forEach(card => {
            card.style.display =
                (stats === 'all product' || card.dataset.stats === stats)
                    ? ''
                    : 'none';
        });
    }

    // Order actions
    const orderPrep = (id, action) => {
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        let actions = '', label = '', requestData = {};

        switch (action) {
            case 'cancelled':
                actions = 'cancel this transaction!';
                label = 'Cancel Order';
                requestData = { action: 'cancelled', _method: 'PUT' };
                break;
            case 'completed':
                actions = 'complete this transaction!';
                label = 'Complete Order';
                requestData = { action: 'completed', _method: 'PUT' };
                break;
            default:
                actions = 'perform this action!';
                label = 'Confirm';
                break;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `You want to ${actions}`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${label}`
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/sale/update/${id}`,
                    method: "POST",
                    data: requestData,
                    dataType: 'json',
                    success: function (data) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => window.location.reload());
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'An error occurred while processing the request.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    };
</script>
@endsection
