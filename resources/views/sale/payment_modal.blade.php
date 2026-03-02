{{-- PAYMENT MODAL --}}
<input type="checkbox" id="payment_modal" class="modal-toggle" />
<div class="modal">
    <div class="modal-box max-w-sm">
        <h3 class="font-bold text-lg mb-4">Complete Payment</h3>

        <div class="space-y-3">
            <div>
                <label class="label">
                    <span class="label-text text-sm">Total Payment</span>
                </label>
                <x-text-input id="modal_total"
                    readonly />
            </div>

            <div>
                <label class="label">
                    <span class="label-text text-sm">Amount Received</span>
                </label>
                <x-text-input type="number"
                    id="amount_received"
                    placeholder="Enter amount"
                    oninput="computeChange()" />
            </div>

            <div>
                <label class="label">
                    <span class="label-text text-sm">Change (Sukli)</span>
                </label>
                <x-text-input
                    id="change_amount"
                    readonly />
            </div>
        </div>

        <div class="modal-action">
            <label for="payment_modal" class="btn btn-ghost">Cancel</label>
            <x-button onclick="confirmOrder()" size="md" color="primary">
                Confirm Payment
            </x-button>
        </div>
    </div>
</div>
