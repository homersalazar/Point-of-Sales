

<div id="customerTable">
    <x-table  :headers="['Name', 'Email', 'Phone no.', 'Address', 'Action']">
        @forelse ($customers as $row)
            <tr>
                <th>{{ $row->name }}</th>
                <td>{{ $row->email }}</td>
                <td>{{ $row->phone }}</td>
                <td>{{ $row->address }}</td>
                <td>
                    <div class="flex flex-row gap-2 w-full">
                        <x-button
                            color="info"
                            outline
                            onclick="update_customer('{{ $row->id }}', '{{ $row->name }}', '{{ $row->email }}', '{{ $row->phone }}', '{{ $row->address }}')"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </x-button>

                        <x-button
                            color="error"
                            outline
                            onclick="delete_customer('{{ $row->id }}')"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center text-gray-500">No customers found.</td>
            </tr>
        @endforelse
    </x-table>

    @if ($customers instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    @endif
</div>

<script>
    const update_customer = (id, name, email, phone, address) => {
        // Open modal
        document.getElementById('update_customer_modal').checked = true;

        // Fill inputs
        document.getElementById('name').value = name;
        document.getElementById('email').value = email;
        document.getElementById('phone').value = phone;
        document.getElementById('address').value = address;

        // Handle form submit
        $("#updateCustomerForm").off("submit").on("submit", function (e) {
            e.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: `/customer/update_customer/${id}`,
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

    const delete_customer = id => {
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
                    url: `/customer/delete_customer/${id}`,
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
