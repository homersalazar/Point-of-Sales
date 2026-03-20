

<div>
    <x-table
        id="purchase_order_table"
        :headers="['PO Number', 'Supplier Name', 'Total Amount', 'Status', 'Action']">
    </x-table>
</div>

<script>
    const updateStatus = (id, action) => {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        let actions = '';
        let label = '';
        let requestData = {};

        switch (action) {
            case 'cancelled':
                actions = 'cancel this purchase order!';
                label = 'Cancel Purchase Order';
                requestData = {
                    action: 'cancelled',
                    _method: 'PUT'
                };
                break;

            case 'completed':
                actions = 'complete this purchase order!';
                label = 'Approve Purchase Order';
                requestData = {
                    action: 'completed',
                    _method: 'PUT'
                };
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
            confirmButtonText: `Yes, ${label}` // ✅ now works
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/purchase_order/update_status/${id}`,
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
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function (xhr) {
                        let errorMessage = xhr.responseJSON?.message
                            ?? 'An error occurred while processing the request.';

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    };

    const viewPurchaseOrder = (id, poNumber, supplierName, totalAmount, status) => {
        // Open modal (DaisyUI modal)
        document.getElementById('viewPurchaseOrderModal').checked = true;

        // Fill the fields
        $('#view_po_number').text(poNumber);
        $('#view_supplier_name').text(supplierName);
        $('#view_total_amount').text('₱' + parseFloat(totalAmount).toFixed(2));
        $('#view_total_amount_footer').text('₱' + parseFloat(totalAmount).toFixed(2));
        // Status badge
        const statusEl = $('#view_status');
        statusEl.text(status.charAt(0).toUpperCase() + status.slice(1));

        // Remove existing classes and add new color class based on status
        statusEl.removeClass();
        statusEl.addClass('px-3 py-1 rounded-lg font-semibold text-sm ' +
            (status === 'Pending' ? 'bg-yellow-100 text-yellow-700' :
            status === 'Cancelled' ? 'bg-red-100 text-red-700' :
            status === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'));

        // Fetch purchase order items via AJAX
        $.ajax({
                url: "{{ route('purchase_order.items') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    po_id: id,
                },
                success: function (data) {
                    $('#view_items_table').html(data);
                },
                error: function (xhr, status, error) {
                    console.error("Error:", error);
                },
            });
    };

    $(document).ready(function(){
        $('#purchase_order_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('purchase_order.data') }}",
            columns: [
                {data: 'po_no'},
                {data: 'name'},
                {data: 'total_amount'},
                {data: 'status'},
                {data: 'action'}
            ]
        });
    });
</script>
