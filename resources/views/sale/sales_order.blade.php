@extends('layouts.app')

@section('content')
    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            {{-- Left Side --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-base-content">
                        Sales Order
                    </h1>
                    <p class="text-sm text-base-content/50 mt-0.5">
                        {{ \Carbon\Carbon::now()->format('F d, Y') }}
                    </p>
                </div>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3 w-full md:w-auto">

                <!-- Search -->
                <div class="w-full md:w-72">
                    <x-search-input
                        placeholder="Search..."
                    />
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        {{-- Filter and Viewer --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div class="flex gap-2 mb-5 overflow-x-auto">

                    <button
                        class="btn btn-ghost flex-shrink-0 w-32 h-auto py-2.5 px-4 flex-col items-start rounded-xl border border-base-300 bg-white normal-case hover:bg-yellow-50 hover:border-yellow-400 transition-all bg-yellow-500 !border-yellow-500 text-white hover:!bg-yellow-600">
                        <span class="font-bold text-sm text-white">All</span>
                    </button>
                    <button
                        class="btn btn-ghost flex-shrink-0 w-32 h-auto py-2.5 px-4 flex-col items-start rounded-xl border border-base-300 bg-white normal-case hover:bg-yellow-50 hover:border-yellow-400 transition-all bg-yellow-500 !border-yellow-500 text-white hover:!bg-yellow-600">
                        <span class="font-bold text-sm text-white">Pending</span>
                    </button>
                    <button
                        class="btn btn-ghost flex-shrink-0 w-32 h-auto py-2.5 px-4 flex-col items-start rounded-xl border border-base-300 bg-white normal-case hover:bg-yellow-50 hover:border-yellow-400 transition-all bg-yellow-500 !border-yellow-500 text-white hover:!bg-yellow-600">
                        <span class="font-bold text-sm text-white">Completed</span>
                    </button>
            </div>

            <!-- Viewer -->
            <div class="join mb-4">
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

        {{-- Content --}}
        <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @include('sale.partials.order_card')
        </div>

        <div id="tableView" class="hidden bg-base-100 border border-base-200 rounded-2xl shadow-sm p-6">
            <x-entries />
            @include('sale.partials.order_table')
        </div>
    </div>

    <script>
        document.querySelectorAll('.view-toggle').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('gridView').classList.toggle('hidden', this.value !== 'grid');
                document.getElementById('tableView').classList.toggle('hidden', this.value !== 'table');
            });
        });
    </script>
@endsection
