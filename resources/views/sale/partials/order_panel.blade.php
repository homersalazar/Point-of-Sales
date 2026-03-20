{{-- sale/partials/order_panel.blade.php --}}

<h2 class="text-lg font-extrabold text-base-content mb-4 hidden lg:block">Detail Order</h2>

{{-- Customer --}}
<label class="label py-0 mb-1.5">
    <span class="label-text text-xs font-semibold text-base-content/50 uppercase tracking-wide">Customer</span>
</label>
<x-select name="customer_id" size="sm" caption="Type or Select Customer">
    @foreach ($customers as $row)
        <option value="{{ $row->id }}">{{ $row->name }}</option>
    @endforeach
</x-select>

<p class="text-sm font-semibold text-base-content mt-4 mb-3">Your order:</p>

{{-- Order Items --}}
<div class="order-items-container flex-1 overflow-y-auto scrollbar-thin divide-y divide-base-200 -mx-1 px-1 min-h-[80px]">
    <p class="text-sm text-base-content/40 text-center py-8">No items added yet.</p>
</div>

{{-- Summary --}}
<div class="border-t border-base-200 pt-4 mt-3 space-y-2">
    <div class="flex justify-between items-center">
        <span class="text-sm text-base-content/50">Subtotal (<span class="order-item-count">0</span>)</span>
        <span class="text-sm font-semibold order-subtotal">₱ 0</span>
    </div>
    <div class="divider my-1"></div>
    <div class="flex justify-between items-center">
        <span class="text-sm font-bold text-base-content">Total payment</span>
        <span class="text-base font-extrabold text-base-content order-total">₱ 0</span>
    </div>
</div>

{{-- Cash Fields --}}
<div class="cash-fields flex flex-col gap-2 transition-all duration-300 mt-2">
    <div>
        <label class="label py-0 mb-1">
            <span class="label-text text-xs font-semibold text-base-content/50 uppercase tracking-wide">Received</span>
        </label>
        <input type="number" class="amount-received-input input input-bordered input-sm w-full" placeholder="0.00" oninput="computeChange()">
    </div>
    <div>
        <label class="label py-0 mb-1">
            <span class="label-text text-xs font-semibold text-base-content/50 uppercase tracking-wide">Change (Sukli)</span>
        </label>
        <input type="text" class="change-amount-input input input-bordered input-sm w-full font-bold text-green-600" readonly>
    </div>
</div>

{{-- Payment Method --}}
<div class="mt-2">
    <label class="label py-0">
        <span class="label-text text-xs font-semibold text-base-content/50 uppercase tracking-wide">Payment method *</span>
    </label>
    <x-select name="payment_method" size="sm" class="mb-4" onchange="togglePaymentMethod()">
        <option value="cash">Cash</option>
        <option value="gcash">GCash</option>
    </x-select>
</div>

<button onclick="confirmOrder()" class="btn btn-primary w-full font-bold text-base gap-2 rounded-xl">
    Make Order
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/>
    </svg>
</button>
