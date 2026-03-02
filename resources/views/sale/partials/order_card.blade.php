@forelse ($sales as $row)
    <div class="order-card card bg-base-100 shadow-xl w-full rounded-3xl p-6 flex flex-col max-h-[430px]" data-stats="{{ $row['sales_status'] }}">
        <!-- Header -->
        <div class="flex items-start justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="avatar placeholder">
                    <div class="bg-success text-success-content rounded-2xl w-11 text-base font-bold">
                        <span>{{ strtoupper(substr($row['customer_name'], 0, 2)) }}</span>
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-base-content text-[15px] leading-tight">{{ $row['customer_name'] }}</p>
                    <p class="text-xs text-base-content/50 mt-0.5">Order #{{ $row['order_no'] }}</p>
                </div>
            </div>

            @php
                $statusClass = match($row['payment_method']) {
                    'cash' => 'badge-success',
                    'gcash' => 'badge-info',
                    default => 'badge-ghost',
                };

                $dotClass = match($row['payment_method']) {
                    'cash' => 'fa-peseta-sign',
                    'gcash' => 'fa-wallet',
                    default => 'bg-base-content',
                };
            @endphp

            <div class="badge {{ $statusClass }} badge-soft gap-1.5 font-semibold text-xs px-3 py-3 rounded-full">
                <i class="fa-solid {{ $dotClass }}"></i>
                {{ ucfirst($row['payment_method']) }}
            </div>
        </div>

        <!-- Date / Time -->
        <div class="flex justify-between text-xs text-base-content/40 mb-4">
            <span>{{ \Carbon\Carbon::parse($row['created_at'])->format('D, F d, Y') }}</span>
            <span>{{ \Carbon\Carbon::parse($row['created_at'])->format('h:i A') }}</span>
        </div>

        <div class="divider my-0 mb-4"></div>

        <!-- Column Headers -->
        <div class="grid grid-cols-[1fr_36px_64px] text-[10px] font-semibold uppercase tracking-widest text-base-content/30 mb-3">
            <span>Items</span>
            <span class="text-center">Qty</span>
            <span class="text-right">Price</span>
        </div>

        <!-- Items -->
        <div class="flex flex-col gap-3 mb-5 flex-1 min-h-0 overflow-y-auto">
            @foreach ($row['items'] as $item)
                <div class="grid grid-cols-[1fr_36px_64px] items-center">
                    <span class="text-sm font-medium text-base-content">{{ $item->item }}</span>
                    <span class="text-sm text-base-content/40 text-center">x{{ $item->qty }}</span>
                    <span class="text-sm font-medium text-base-content text-right">₱{{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>


        <!-- Total -->
        <div class="flex justify-between items-center border-t-2 border-dashed border-base-300 pt-4 mb-5">
            <span class="text-sm font-semibold text-base-content/60">Total</span>
            <span class="text-2xl font-bold text-base-content">₱{{ number_format($row['total_amount'], 2) }}</span>
        </div>

        @if ($row['sales_status'] == 'pending')
            <div class="flex flex-wrap gap-3 w-full">
                <x-button
                    color="error"
                    class="text-white w-full sm:w-auto"
                    onclick="orderPrep('{{ $row['order_no'] }}', 'cancelled')">
                    Cancel Order
                </x-button>

                <x-button
                    color="success"
                    class="text-white w-full sm:w-auto"
                    onclick="orderPrep('{{ $row['order_no'] }}', 'completed')">
                    Complete Order
                </x-button>
            </div>
        @else
            <x-status :status="$row['sales_status']" />
        @endif
    </div>
@empty
    <p class="text-center text-base-content/50 col-span-full">No orders found.</p>
@endforelse
