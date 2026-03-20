

<div>
    <x-table
        id="expenseTable"
        :headers="['Name', 'Amount', 'Expense Date', 'Description', 'Status', 'Action']">
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
                actions = 'cancel this expense!';
                label = 'Cancel Expense'; // ✅ FIX HERE
                requestData = {
                    action: 'cancelled',
                    _method: 'PUT'
                };
                break;

            case 'completed':
                actions = 'complete this expense!';
                label = 'Complete Expense'; // ✅ FIX HERE
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
                    url: `/expense/update_status/${id}`,
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

    const update_expense = (id, category_id, amount, description, expense_date) => {
        // Open modal
        document.getElementById('update_expense_modal').checked = true;

        // Fill inputs
        document.getElementById('expense_category_id').value = category_id;
        document.getElementById('amount').value = amount;
        document.getElementById('description').value = description;
        document.getElementById('expense_date').value = expense_date;

        // Handle form submit
        $("#updateExpenseForm").off("submit").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: `/expense/update/${id}`,
                method: "POST",
                data: formData,
                success: function(data){
                    Swal.fire({
                    title: data.status === 'success' ? 'Success!' : 'Info!',
                        text: data.message,
                        icon: data.status,
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr){
                    let message = 'An error occurred while updating the category.';
                    if(xhr.responseJSON && xhr.responseJSON.message){
                        message = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error!',
                        text: message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 4000
                    });
                }
            });
        });
    }

    const delete_expense = id => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                    $.ajax({
                    url: `/expense/delete_expense/${id}`,
                    method:"POST",
                    data: {
                        '_token': '{{ csrf_token() }}',
                        '_method': 'delete'
                    },
                    success:function(data){
                        Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 2000
                        }).then((result) => {
                            window.location.reload();
                        });
                    },
                    error: function (xhr) {
                        let message = 'An error occurred while deleting the event.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        else if (xhr.responseText) {
                            try {
                                let parsed = JSON.parse(xhr.responseText);
                                if (parsed.message) {
                                    message = parsed.message;
                                }
                            } catch (e) {
                                message = xhr.responseText;
                            }
                        }
                        Swal.fire({
                            title: "Info!",
                            text: message,
                            icon: "info",
                            showConfirmButton: false,
                            timer: 4000
                        });
                    }
                });
            }
        });
    }

    $(document).ready(function(){
        $('#expenseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('expense.data') }}",
            columns: [
                {data: 'name'},
                {data: 'amount'},
                {data: 'expense_date'},
                {data: 'description'},
                {data: 'status'},
                {data: 'action'}
            ]
        });
    });
</script>
