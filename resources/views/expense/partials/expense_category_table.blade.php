

<div id="expenseCategoryTable">
    <x-table  :headers="['Name', 'Action']">
        @forelse ($exp_categories as $row)
            <tr>
                <th>{{ $row->name }}</th>
                <td>
                    <div class="flex flex-row gap-2 w-full">
                        <x-button
                            color="info"
                            outline
                            onclick="update_exp_category('{{ $row->id }}', '{{ $row->name }}')"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </x-button>

                        <x-button
                            color="error"
                            outline
                            onclick="delete_expense_category('{{ $row->id }}')"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-gray-500">No expense categories found.</td>
            </tr>
        @endforelse
    </x-table>

    @if ($exp_categories instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $exp_categories->links() }}
        </div>
    @endif
</div>

<script>
    const update_exp_category = (ctgy_id, ctgy_name, ) => {
        // Open modal
        document.getElementById('update_exp_category_modal').checked = true;

        // Fill inputs
        document.getElementById('update_name').value = ctgy_name;

        // Handle form submit
        $("#updateExpenseCategoryForm").off("submit").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: `/expense/update_exp_category/${ctgy_id}`,
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

    const delete_expense_category = id => {
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
                    url: `/expense/delete_expense_category/${id}`,
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
</script>
