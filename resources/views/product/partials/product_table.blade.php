
<div id="productTable">
    <x-table
        :headers="['', 'Name', 'SKU', 'Cost Price', 'Selling Price', 'Stock', 'Action']"
    >
        @forelse ($products as $row)
            <tr>
                <th>
                    <x-checkbox />
                </th>
                <td>
                    <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="mask mask-squircle h-12 w-12">
                                <img
                                    src="{{ asset('storage/product/' . $row->image) }}"
                                    alt="{{ $row->name }}"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="font-bold">{{ $row->name }}</div>
                        </div>
                    </div>
                </td>
                <td>{{ $row->sku }}</td>
                <td>₱{{ $row->cost_price }}</td>
                <td>₱{{ $row->selling_price }}</td>
                <td>{{ $row->stock }}</td>
                <td>
                    <div class="flex flex-row gap-2 w-full">
                        <x-button
                            color="info"
                            outline
                            onclick="update_product('{{ $row->id }}', '{{ $row->category_id }}', '{{ $row->name }}', '{{ $row->cost_price }}', '{{ $row->selling_price }}', '{{ $row->stock }}', '{{ $row->image }}')"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                        </x-button>

                        <x-button
                            color="error"
                            outline
                            onclick="delete_product('{{ $row->id }}')"
                        >
                            <i class="fa-solid fa-trash-can"></i>
                        </x-button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-gray-500">No products found.</td>
            </tr>
        @endforelse
    </x-table>

    @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>

<script>
    const update_product = (prod_id, ctgy_id, prod_name, cost_price, selling_price, stock, image) => {
        // Open modal
        document.getElementById('update_product_modal').checked = true;

        // Set the category select to the correct category
        const categorySelect = document.getElementById('category_id');
        categorySelect.value = ctgy_id;

        // Fill inputs
        document.getElementById('name').value = prod_name;
        document.getElementById('cost_price').value = cost_price;
        document.getElementById('selling_price').value = selling_price;
        document.getElementById('stock').value = stock;
        document.getElementById('image').value = '';

        // Optional: show current image in modal
        if (image) {
            document.getElementById('current_image').src = `/storage/product/${image}`;
        } else {
            document.getElementById('current_image').src = '';
        }

        // Handle form submit
        $("#updateProductForm").off("submit").on("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            $.ajax({
                url: `/product/update_product/${prod_id}`,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    Swal.fire({
                        title: data.status === 'success' ? 'Success!' : 'Info!',
                        text: data.message,
                        icon: data.status,
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => window.location.reload());
                },
                error: function(xhr){
                    let message = 'An error occurred.';
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

    const delete_product = id => {
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
                    url: `/product/delete_product/${id}`,
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
