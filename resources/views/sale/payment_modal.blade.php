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
                <input type="text" id="modal_total"
                    class="input input-bordered w-full font-bold"
                    readonly>
            </div>

            <div>
                <label class="label">
                    <span class="label-text text-sm">Amount Received</span>
                </label>
                <input type="number"
                    id="amount_received"
                    class="input input-bordered w-full"
                    placeholder="Enter amount"
                    oninput="computeChange()">
            </div>

            <div>
                <label class="label">
                    <span class="label-text text-sm">Change (Sukli)</span>
                </label>
                <input type="text"
                    id="change_amount"
                    class="input input-bordered w-full font-bold"
                    readonly>
            </div>
        </div>

        <div class="modal-action">
            <label for="payment_modal" class="btn btn-ghost">Cancel</label>
            <button onclick="confirmOrder()" class="btn btn-primary">
                Confirm Payment
            </button>
        </div>
    </div>
</div>
