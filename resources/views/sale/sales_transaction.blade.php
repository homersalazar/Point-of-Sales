@extends('layouts.app')

@section('content')
    @include('product.create_product_modal')
    @include('sale.payment_modal')
    <div class="flex h-screen bg-base-200 overflow-hidden">

        {{-- ══════════════ LEFT PANEL ══════════════ --}}
        <div class="flex flex-col flex-1 overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h1 class="text-2xl font-bold text-base-content">
                        Sales Transaction
                    </h1>
                    <p class="text-sm text-base-content/50 mt-0.5">
                        {{ \Carbon\Carbon::now()->format('F d, Y') }}
                    </p>
                </div>

                <!-- Search -->
                <div class="w-full md:w-72 px-2">
                    <label class="input input-bordered input-sm flex items-center gap-2">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search..."
                            class="grow"
                            placeholder="Search"
                            oninput="filterProducts()"
                        />
                        <i class="fa-solid fa-magnifying-glass w-4 h-4"></i>
                    </label>
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="flex gap-2 mb-5 overflow-x-auto">
                @foreach($cats as $cat)
                    <button
                        onclick="setCategory(this, '{{ $cat->id }}')"
                        data-cat="{{ $cat->id }}"
                        class="cat-tab btn btn-ghost flex-shrink-0 w-32 h-auto py-2.5 px-4 flex-col items-start rounded-xl border border-base-300 bg-white normal-case hover:bg-yellow-50 hover:border-yellow-400 transition-all {{ $cat->id === 0 ? '!bg-yellow-500 !border-yellow-500 text-white hover:!bg-yellow-600' : '' }}">
                        <span class="font-bold text-sm {{ $cat->id === 0 ? 'text-white' : 'text-base-content' }}">{{ $cat->name }}</span>
                        <span class="text-xs font-normal mt-0.5 {{ $cat->id === 0 ? 'text-white/70' : 'text-base-content/50' }}">{{ $cat->total_products }}</span>
                    </button>
                @endforeach
            </div>


            {{-- Product Grid --}}
            <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 overflow-y-auto scrollbar-thin pr-1">

                {{-- Add New Product Card --}}
                <x-button
                    click="add_product"
                    class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-yellow-200 bg-yellow-50/60 cursor-pointer hover:bg-yellow-100 hover:border-yellow-400 transition-all min-h-[160px] group h-44 w-full">

                    <div class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center text-yellow-500 text-2xl font-light group-hover:scale-110 transition-transform">
                        +
                    </div>

                    <span class="text-sm font-semibold text-yellow-500">Add New Product</span>
                </x-button>


                @foreach($products as $row)
                    <div class="product-card card card-compact bg-base-100 w-full shadow-sm text-sm h-44 cursor-pointer hover:shadow-md hover:scale-[1.02] transition-all duration-200"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        data-price="{{ $row->selling_price }}"
                        data-img="{{ $row->image }}"
                        data-category="{{ $row->category_id }}"
                        onclick="addToOrder(this)"
                    >
                        <figure class="h-24 overflow-hidden">
                            <img src="{{ asset('storage/product/' . $row->image) }}"
                                alt="{{ $row->name }}"
                                class="h-full w-full object-fit" />
                        </figure>
                        <div class="py-2 px-2.5">
                            <h2 class="card-title text-base">{{ $row->name }}</h2>
                            <p class="text-sm text-neutral">₱ {{ number_format($row->selling_price ,2) }}</p>
                        </div>
                    </div>

                @endforeach
            </div>
        </div>

        {{-- ══════════════ RIGHT PANEL ══════════════ --}}
        <div class="w-[300px] border-l border-yellow-200 px-5 flex flex-col overflow-hidden">
            <h2 class="text-lg font-extrabold text-base-content mb-5">Detail Order</h2>

            {{-- Customer --}}
            <label class="label py-0 mb-1.5">
                <span class="label-text text-xs font-semibold text-base-content/50 uppercase tracking-wide">Customer</span>
            </label>
            <x-select name="customer_id" size="sm" caption="Type or Select Customer">
                @foreach ($customers as $row)
                    <option value="{{ $row->id }}">{{ $row->name }}</option>
                @endforeach
            </x-select>

            <p class="text-sm font-semibold text-base-content mt-4 mb-3">Your order :</p>

            {{-- Order Items --}}
            <div id="orderItems" class="flex-1 overflow-y-auto scrollbar-thin divide-y divide-base-200 -mx-1 px-1">
                <p id="emptyMsg" class="text-sm text-base-content/40 text-center py-8">No items added yet.</p>
            </div>

            {{-- Summary --}}
            <div class="border-t border-base-200 pt-4 mt-3 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-base-content/50">Subtotal (<span id="itemCount">0</span>)</span>
                    <span class="text-sm font-semibold" id="subtotal">₱ 0</span>
                </div>
                <div class="divider my-1"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-base-content">Total payment</span>
                        <span class="text-base font-extrabold text-base-content" id="total">₱ 0</span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="mt-4">
                    <label class="label py-0 mb-1.5">
                        <span class="label-text text-xs font-semibold text-base-content/50 uppercase tracking-wide">Payment method *</span>
                    </label>
                    <x-select name="payment_method" size="sm" class="mb-4">
                        <option value="cash">Cash</option>
                        <option value="gcash">Gcash</option>
                    </x-select>
                </div>

                <button onclick="makeOrder()" class="btn btn-primary w-full font-bold text-base gap-2 rounded-xl">
                    Make Order
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9l20-7z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>


    <script>
        const order = {};

        function fmt(n) {
            return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }


        function addToOrder(card) {
            const id = card.dataset.id;

            if (!order[id]) {
                order[id] = {
                    id,
                    name: card.dataset.name,
                    price: parseFloat(card.dataset.price),
                    img: card.dataset.img,
                    qty: 0
                };
            }

            order[id].qty++;
            card.classList.add('!border-base-500', 'ring-2', 'ring-primary');
            renderOrder();
        }

        function changeQty(id, delta) {
            if (!order[id]) return;

            order[id].qty += delta;

            if (order[id].qty <= 0) {
                delete order[id];
                const card = document.querySelector(`.product-card[data-id="${id}"]`);
                if (card) card.classList.remove('!border-base-500', 'ring-2', 'ring-primary');
            }

            renderOrder();
        }

        function removeItem(id) {
            delete order[id];
            const card = document.querySelector(`.product-card[data-id="${id}"]`);
            if (card) card.classList.remove('!border-base-500', 'ring-2', 'ring-primary');
            renderOrder();
        }

        function renderOrder() {
            const container = document.getElementById('orderItems');
            const ids = Object.keys(order);

            if (ids.length === 0) {
                container.innerHTML = '<p class="text-sm text-base-content/40 text-center py-8">No items added yet.</p>';
                document.getElementById('itemCount').textContent = 0;
                document.getElementById('subtotal').textContent = fmt(0);
                document.getElementById('total').textContent = fmt(0);
                return;
            }

            let subtotal = 0;
            let html = '';
            const assetBase = "{{ asset('storage/product') }}";

            ids.forEach(id => {
                const item = order[id];
                const lineTotal = item.price * item.qty;
                subtotal += lineTotal;

                html += `
                    <div class="flex items-center gap-3 py-3 bg-white p-2 rounded-xl mb-1">
                        <img src="${assetBase}/${item.img}" class="w-11 h-11 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-base-content truncate">${item.name}</p>
                            <div class="flex items-center gap-2">
                                <button onclick="changeQty('${id}', -1)" class="btn btn-xs btn-outline btn-square rounded-lg w-6 h-6 min-h-0 text-base-500 border-base-300 hover:bg-base-50 hover:border-base-400 hover:text-base-600">−</button>
                                <span class="text-sm font-bold w-4 text-center">${item.qty}</span>
                                <button onclick="changeQty('${id}', 1)" class="btn btn-xs btn-outline btn-square rounded-lg w-6 h-6 min-h-0 text-base-500 border-base-300 hover:bg-base-50 hover:border-base-400 hover:text-base-600">+</button>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <button onclick="removeItem('${id}')" class="btn btn-ghost btn-xs btn-square text-error hover:bg-red-50 p-0 min-h-0 h-auto">
                                <i class="fa-solid fa-trash-can w-4 h-4"></i>
                            </button>
                            <span class="text-sm font-bold text-base-content">${fmt(lineTotal)}</span>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;

            document.getElementById('itemCount').textContent = ids.length;
            document.getElementById('subtotal').textContent = fmt(subtotal);
            document.getElementById('total').textContent = fmt(subtotal);
        }

        function setCategory(el, cat) {
            document.querySelectorAll('.cat-tab').forEach(t => {
                t.classList.remove('!bg-yellow-500', '!border-yellow-500');
                t.querySelectorAll('span').forEach((s, i) => {
                    s.classList.remove('text-white', 'text-white/70');
                    s.classList.add(i === 0 ? 'text-base-content' : 'text-base-content/50');
                });
            });

            el.classList.add('!bg-yellow-500', '!border-yellow-500');
            el.querySelectorAll('span').forEach((s, i) => {
                s.classList.remove('text-base-content', 'text-base-content/50');
                s.classList.add(i === 0 ? 'text-white' : 'text-white/70');
            });

            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = (cat === '0' || card.dataset.category === cat) ? '' : 'none';
            });
        }

        function filterProducts() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = card.dataset.name.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        const makeOrder = () => {
            if (!Object.keys(order).length) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Please add items to your order first.',
                    icon: 'warning',
                    showConfirmButton: false,
                    timer: 4000
                });
                return;
            }

            const subtotal = Object.values(order).reduce((sum, item) => {
                return sum + (item.price * item.qty);
            }, 0);

            // Show total inside modal
            document.getElementById('modal_total').value = fmt(subtotal);
            document.getElementById('modal_total').dataset.total = subtotal;

            document.getElementById('amount_received').value = '';
            document.getElementById('change_amount').value = '';

            // Open modal
            document.getElementById('payment_modal').checked = true;
        }

        const computeChange = () => {
            const total = parseFloat(document.getElementById('modal_total').dataset.total);
            const received = parseFloat(document.getElementById('amount_received').value) || 0;

            const change = received - total;

            const changeInput = document.getElementById('change_amount');

            if (change >= 0) {
                changeInput.value = fmt(change);

                // GREEN when valid
                changeInput.classList.remove('text-red-600');
                changeInput.classList.add('text-green-600');

            } else {
                changeInput.value = "Insufficient amount";

                // RED when insufficient
                changeInput.classList.remove('text-green-600');
                changeInput.classList.add('text-red-600');
            }
        };

        const confirmOrder = () => {

            const total = parseFloat(document.getElementById('modal_total').dataset.total);
            const received = parseFloat(document.getElementById('amount_received').value) || 0;

            if (received < total) {
                Swal.fire({
                    title: 'Insufficient Payment',
                    text: 'Amount received is less than total payment.',
                    icon: 'warning',
                    timer: 3000,
                    showConfirmButton: false
                });
                return;
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const customer_id = document.querySelector('select[name="customer_id"]').value;
            const payment_method = document.querySelector('select[name="payment_method"]').value;

            $.ajax({
                url: "{{ route('sale.store') }}",
                method: "POST",
                contentType: 'application/json',
                data: JSON.stringify({
                    customer_id,
                    payment_method,
                    amount_received: received,
                    change: received - total,
                    items: Object.values(order),
                }),
                success: function(data) {

                    document.getElementById('payment_modal').checked = false;

                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        Object.keys(order).forEach(id => delete order[id]);
                        renderOrder();
                    });
                },
                error: function(xhr){
                    Swal.fire({
                        title: 'Error!',
                        text: 'Something went wrong.',
                        icon: 'error'
                    });
                }
            });
        }
    </script>
@endsection
