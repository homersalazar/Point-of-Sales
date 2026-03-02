<x-modal id="viewPurchaseOrderModal" title="View Purchase Order Details" size="full">
    <div class="space-y-6">

        <!-- PO Info Card -->
        <div class="bg-base-100 border border-base-200 rounded-2xl shadow-sm p-4 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="space-y-1">
                <p class="text-gray-500 font-medium text-sm">PO Number</p>
                <h2 class="text-base font-semibold" id="view_po_number"></h2>
            </div>
            <div class="space-y-1">
                <p class="text-gray-500 font-medium text-sm">Supplier Name</p>
                <h2 class="text-base font-semibold" id="view_supplier_name"></h2>
            </div>
            <div class="space-y-1">
                <p class="text-gray-500 font-medium text-sm">Total Amount</p>
                <h2 class="text-base font-semibold" id="view_total_amount"></h2>
            </div>
            <div class="space-y-1">
                <p class="text-gray-500 font-medium text-sm">Status</p>
                <span class="px-3 py-1 rounded-lg font-semibold text-sm" id="view_status"></span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-base-100 border border-base-200 rounded-2xl shadow-sm p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product Name</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    </tr>
                </thead>
                <tbody id="view_items_table" class="bg-white divide-y divide-gray-200">
                    <!-- Rows dynamically injected via JS/PHP -->
                    <!-- Make sure each <td> has proper alignment: text-left for text, text-right for numbers -->
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-3 py-2 text-right font-bold text-gray-800">Total:</td>
                        <td class="px-3 py-2 text-right font-bold text-gray-800" id="view_total_amount_footer"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</x-modal>
