
<div>
    <x-table
        id="productTable"
        :headers="['Name', 'SKU', 'Selling Price', 'Stock', 'Action']"
    >
    </x-table>
</div>

<script>
    const update_product = (prod_id, ctgy_id, prod_name, selling_price, image) => {
        // Open modal
        document.getElementById('update_product_modal').checked = true;

        // Set the category select to the correct category
        const categorySelect = document.getElementById('category_id');
        categorySelect.value = ctgy_id;

        // Fill inputs
        document.getElementById('name').value = prod_name;
        document.getElementById('selling_price').value = selling_price;
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

    $(document).ready(function(){
        $('#productTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('product.data') }}",
            columns: [
                {data: 'name'},
                {data: 'sku'},
                {data: 'selling_price'},
                {data: 'stock'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
    });
</script>
