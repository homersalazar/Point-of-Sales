@extends('layouts.app')

@section('content')
    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    Create Purchase Order
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Create and manage your purchase orders efficiently.
                </p>
            </div>

        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        <!-- Content Card -->
        <div class="bg-base-100 border border-base-200 rounded-2xl shadow-sm p-6">
            <div class="flex flex-col gap-5 w-full">
                <!-- Supplier ID -->
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">Supplier ID</label>
                    <x-select form="addPurchaseOrderForm" name="supplier_id" id="supplier_id" class="max-w-72" caption="Select Supplier" required>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </x-select>
                </div>

                <!-- Product Table -->
                <div class="relative overflow-x-auto">
                    <form method="POST" id="addPurchaseOrderForm">
                        @csrf
                        <x-table id="PurchaseOrderTable" :headers="['Product Name', 'Quantity', 'Unit', 'Unit Price', 'Subtotal', '']"></x-table>

                        <div class="flex flex-col max-w-lg py-2">
                            <div class="flex flex-row items-center w-full">
                                <div class="relative w-full p-1">
                                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </div>
                                    <x-text-input
                                        type="text"
                                        id="product"
                                        name="product[]"
                                        class="w-full md:w-[26rem] ps-10 p-2.5"
                                        placeholder="Search Product Name"
                                        autofocus
                                        autocomplete="off"
                                    />
                                </div>

                                <x-button type="submit" color="primary">
                                    Submit
                                </x-button>

                            </div>
                            <div id="productLists"></div>
                        </div>
                        <div id="formFields" style="display: none;"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function ucwords (str) {
            return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
                return $1.toUpperCase();
            });
        }

        $(document).ready(function(){
            $('#product').keyup(function(){
                var query = $(this).val();
                if(query != ''){
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url:"{{ route('purchase_order.fetch_product') }}",
                        method:"POST",
                        data:{
                            query:query,
                            _token:_token
                        },
                        success:function(data){
                            $('#productLists').fadeIn();
                            $('#productLists').html(data);
                        }
                    });
                } else {
                    $('#productLists').fadeOut();
                    $('#productLists').html('');
                }
            });

            $(document).click(function(event) {
                var target = $(event.target);
                if (!target.closest('#product').length && !target.closest('#productLists').length) {
                    $('#productLists').fadeOut();
                }
            });

            $("#addPurchaseOrderForm").off("submit").on("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('purchase_order.store_purchase_order') }}",
                    method: "POST",
                    data: formData,
                    processData: false,  // Don't process the data
                    contentType: false,  // Don't set content type (let browser set it)
                    success: function(data) {
                        if(data.success){
                            Swal.fire({
                                title: 'Success!',
                                icon: 'success',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 3000
                            }).then((result) => {
                                window.location.reload();
                            });
                        }else{
                            Swal.fire({
                                title: 'Error',
                                icon: 'error',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    },
                    error: function (xhr) {
                        let message = 'An error occurred while updating the event.';
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
            });
        });

        // Listen for input changes on quantity or unit_price fields
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('unit_price')) {
                // Find the closest table row
                let row = e.target.closest('tr');

                // Get quantity and cost price values
                let quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                let costPrice = parseFloat(row.querySelector('.unit_price').value) || 0;

                // Calculate subtotal
                let subtotal = quantity * costPrice;

                // Update the subtotal field with peso sign
                let subtotalInput = row.querySelector('.subtotal');
                if (subtotalInput) {
                    subtotalInput.value = subtotal.toFixed(2);
                }
            }
        });

        var items = 0;
        var counter = 1;
        const add_purchase_order = (prod_id, prod_name) => {

            // ✅ Check if product already exists in table
            if ($('#PurchaseOrderTable-body tr[data-product-id="' + prod_id + '"]').length > 0) {
                Swal.fire({
                    title: 'Info!',
                    text: 'Product already added.',
                    icon: 'info',
                    showConfirmButton: false,
                    timer: 4000
                });
                return;
            }

            items++;
            $("#product").val('');
            $('#productLists').fadeOut();
            var unitOptions = @json($units);

            var counterId = counter++;

            var html = '<tr class="bg-white border-b border-gray-200 hover:bg-gray-100" data-row="' + counterId + '" data-product-id="' + prod_id + '">';
                html += '<td>' + prod_name + '</td>';
                html += '<td><input type="number" name="quantity[]" class="quantity input input-bordered input-sm input-primary w-full"></td>';
                html += '<td>';
                    html += '<select name="unit_id[]" class="select select-primary select-sm w-full">';
                    unitOptions.forEach(function(option) {
                        html += '<option value="' + option.id + '">' + ucwords(option.name) + '</option>';
                    });
                    html += '</select>';
                html += '</td>';
                html += '<td><input type="number" name="unit_price[]" min="0" step="any" class="unit_price input input-bordered input-sm input-primary w-full"></td>';
                html += '<td><input type="text" name="subtotal[]" class="subtotal bg-gray-200 input input-bordered input-sm input-primary w-full" readonly></td>';
                html += '<td>';
                    html += '<td><input type="hidden" name="product_id[]" value="' + prod_id + '">';
                    html += '<td class="text-center"><button type="button" onclick="deleteRow(this)" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-xmark"></i></button></td>';
                html += '</td>';
            html += '</tr>';

            $("#PurchaseOrderTable-body").append(html);
        };

        const deleteRow = (button) => {
            items--;
            $(button).closest('tr').remove();
        }
    </script>
@endsection
