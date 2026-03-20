@forelse ($sales as $row)
    <div class="order-card card bg-base-100 shadow-lg w-full rounded-2xl overflow-hidden flex flex-col"
        data-stats="{{ $row['sales_status'] }}">

        <div class="flex flex-col flex-1 p-4">

            {{-- Header: Avatar + Customer Info + Payment Method --}}
            <div class="flex flex-row items-start sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="avatar placeholder flex-shrink-0">
                        <div class="bg-success text-success-content rounded-xl w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center text-sm sm:text-base font-bold">
                            {{ strtoupper(substr($row['customer_name'], 0, 2)) }}
                        </div>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <p class="font-semibold text-sm sm:text-lg text-base-content truncate">{{ $row['customer_name'] }}</p>
                        <p class="text-xs text-base-content/50 mt-0.5">Order #{{ $row['order_no'] }}</p>
                    </div>
                </div>

                @php
                    $statusClass = match($row['payment_method']) {
                        'cash'  => 'badge-success',
                        'gcash' => 'badge-info',
                        default => 'badge-ghost',
                    };
                    $dotClass = match($row['payment_method']) {
                        'cash'  => 'fa-peseta-sign',
                        'gcash' => 'fa-wallet',
                        default => 'bg-base-content',
                    };
                @endphp

                <div class="badge {{ $statusClass }} badge-soft gap-1.5 font-semibold text-xs px-2 sm:px-3 py-2 rounded-full flex-shrink-0 flex items-center whitespace-nowrap">
                    <i class="fa-solid {{ $dotClass }}"></i>
                    {{ ucfirst($row['payment_method']) }}
                </div>
            </div>

            {{-- Date / Time --}}
            <div class="flex flex-row justify-between text-xs text-base-content/40 mb-4 gap-1 flex-wrap">
                <span>{{ \Carbon\Carbon::parse($row['created_at'])->format('D, F d, Y') }}</span>
                <span>{{ \Carbon\Carbon::parse($row['created_at'])->format('h:i A') }}</span>
            </div>

            <div class="divider my-0 mb-4"></div>

            {{-- Items Table Header --}}
            <div class="grid grid-cols-[1fr_36px_64px] sm:grid-cols-[1fr_40px_80px] text-[10px] sm:text-[11px] font-semibold uppercase tracking-widest text-base-content/30 mb-2">
                <span>Items</span>
                <span class="text-center">Qty</span>
                <span class="text-right">Price</span>
            </div>

            {{-- Items List --}}
            <div class="flex flex-col gap-2 mb-4 overflow-y-auto max-h-40 sm:max-h-52">
                @foreach ($row['items'] as $item)
                    <div class="grid grid-cols-[1fr_36px_64px] sm:grid-cols-[1fr_40px_80px] items-center gap-x-1">
                        <span class="text-xs sm:text-sm font-medium text-base-content truncate">{{ $item->item }}</span>
                        <span class="text-xs sm:text-sm text-base-content/50 text-center">x{{ $item->qty }}</span>
                        <span class="text-xs sm:text-sm font-medium text-base-content text-right">₱{{ number_format($item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Total --}}
            <div class="flex flex-row justify-between items-center border-t-2 border-dashed border-base-300 pt-3 mb-4">
                <span class="text-sm font-semibold text-base-content/60">Total</span>
                <span class="text-xl sm:text-2xl font-bold text-base-content">₱{{ number_format($row['total_amount'], 2) }}</span>
            </div>
        </div>

        {{-- Buttons pinned at bottom, outside the padded area --}}
        @if ($row['sales_status'] == 'pending')
            <div class="grid grid-cols-2 border-t border-base-200">
                <button
                    class="btn btn-error btn-soft rounded-none rounded-bl-2xl text-white"
                    onclick="orderPrep('{{ $row['order_no'] }}', 'cancelled')">
                    Cancel
                </button>
                <button
                    class="btn btn-success btn-soft rounded-none rounded-br-2xl text-white"
                    onclick="orderPrep('{{ $row['order_no'] }}', 'completed')">
                    Complete
                </button>
            </div>
        @else
            <div class="px-4 sm:px-6 pb-4 sm:pb-6">
                <x-status :status="$row['sales_status']" />
            </div>
        @endif


    </div>
@empty
    <p class="text-center text-base-content/50 col-span-full">No orders found.</p>
@endforelse
