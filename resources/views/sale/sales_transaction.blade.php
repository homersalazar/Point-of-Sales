@extends('layouts.app')

@section('content')
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

                {{-- <input type="text" id="searchInput" placeholder="Search..." class="grow text-sm" oninput="filterProducts()"> --}}
                <!-- Search -->
                <div class="w-full md:w-72">
                    <x-search-input
                        placeholder="Search..."
                        size="md"
                        oninput="filterProducts()"
                    />
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="flex gap-3 mb-5">
                @php
                    $cats = [
                    ['key'=>'all',      'label'=>'All Product', 'count'=>'1200 items'],
                    ['key'=>'foods',    'label'=>'Foods',       'count'=>'12 items'],
                    ['key'=>'baverage', 'label'=>'Baverage',    'count'=>'12 items'],
                    ['key'=>'other',    'label'=>'Other',       'count'=>'12 items'],
                    ];
                @endphp

                @foreach($cats as $i => $cat)
                    <button
                        onclick="setCategory(this, '{{ $cat['key'] }}')"
                        data-cat="{{ $cat['key'] }}"
                        class="cat-tab btn btn-ghost h-auto py-2.5 px-4 flex-col items-start rounded-xl border border-base-300 bg-white normal-case hover:bg-blue-50 hover:border-blue-400 transition-all {{ $i === 0 ? '!bg-blue-500 !border-blue-500 text-white hover:!bg-blue-600' : '' }}">
                        <span class="font-bold text-sm {{ $i === 0 ? 'text-white' : 'text-base-content' }}">{{ $cat['label'] }}</span>
                        <span class="text-xs font-normal mt-0.5 {{ $i === 0 ? 'text-white/70' : 'text-base-content/50' }}">{{ $cat['count'] }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Product Grid --}}
            <div id="productGrid" class="grid grid-cols-5 gap-3 overflow-y-auto scrollbar-thin pr-1 flex-1">

                {{-- Add New Product Card --}}
                <div onclick="alert('Add new product')"
                    class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-indigo-200 bg-indigo-50/60 cursor-pointer hover:bg-indigo-100 hover:border-indigo-400 transition-all min-h-[160px] group h-44">
                    <div class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center text-indigo-500 text-2xl font-light group-hover:scale-110 transition-transform">+</div>
                    <span class="text-sm font-semibold text-indigo-500">Add New Product</span>
                </div>

                @foreach($products as $row)
                    <div class="card card-compact bg-base-100 w-full shadow-sm text-sm h-44 cursor-pointer hover:shadow-md hover:scale-[1.02] transition-all duration-200"
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
        <div class="w-[300px] border-l border-base-200 flex flex-col p-5 overflow-hidden">
            <h2 class="text-lg font-extrabold text-base-content mb-5">Detail Order</h2>

            {{-- Customer --}}
            <label class="label py-0 mb-1.5">
                <span class="label-text text-xs font-semibold text-base-content/50 uppercase tracking-wide">Customer</span>
            </label>
            <x-select name="category_id" size="sm" caption="Type or Select Customer">
                <option value="1">Student 1</option>
                <option value="2">Man</option>
                <option value="3">Women</option>
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
                document.getElementById('tax').textContent = fmt(0);
                document.getElementById('total').textContent = fmt(0);
                return;
            }

            let subtotal = 0;
            let html = '';

            ids.forEach(id => {
                const item = order[id];
                const lineTotal = item.price * item.qty;
                subtotal += lineTotal;

                html += `
                    <div class="flex items-center gap-3 py-3 bg-white p-2 rounded-xl mb-1">
                        <img src="{{ asset('storage/product') }}/${item.img}" class="w-11 h-11 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-base-content truncate">${item.name}</p>
                            <div class="flex items-center gap-2">
                                <button onclick="changeQty('${id}', -1)" class="btn btn-xs btn-outline btn-square rounded-lg w-6 h-6 min-h-0 text-blue-500 border-base-300 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-600">−</button>
                                <span class="text-sm font-bold w-4 text-center">${item.qty}</span>
                                <button onclick="changeQty('${id}', 1)" class="btn btn-xs btn-outline btn-square rounded-lg w-6 h-6 min-h-0 text-blue-500 border-base-300 hover:bg-blue-50 hover:border-blue-400 hover:text-blue-600">+</button>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">
                            <button onclick="removeItem('${id}')" class="btn btn-ghost btn-xs btn-square text-error hover:bg-red-50 p-0 min-h-0 h-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14H6L5 6"/>
                                    <path d="M10 11v6"/>
                                    <path d="M14 11v6"/>
                                    <path d="M9 6V4h6v2"/>
                                </svg>
                            </button>
                            <span class="text-sm font-bold text-base-content">${fmt(lineTotal)}</span>
                        </div>
                    </div>
                `;
            });

            container.innerHTML = html;

            const tax = Math.round(subtotal * 0.1);
            document.getElementById('itemCount').textContent = ids.length;
            document.getElementById('subtotal').textContent = fmt(subtotal);
            document.getElementById('tax').textContent = fmt(tax);
            document.getElementById('total').textContent = fmt(subtotal + tax);
        }

        function setCategory(el, cat) {
            document.querySelectorAll('.cat-tab').forEach(t => {
                t.classList.remove('!bg-blue-500', '!border-blue-500');
                t.querySelectorAll('span').forEach((s, i) => {
                    s.classList.remove('text-white', 'text-white/70');
                    s.classList.add(i === 0 ? 'text-base-content' : 'text-base-content/50');
                });
            });

            el.classList.add('!bg-blue-500', '!border-blue-500');
            el.querySelectorAll('span').forEach((s, i) => {
                s.classList.remove('text-base-content', 'text-base-content/50');
                s.classList.add(i === 0 ? 'text-white' : 'text-white/70');
            });

            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = (cat === 'all' || card.dataset.category === cat) ? '' : 'none';
            });
        }

        function filterProducts() {
            const q = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = card.dataset.name.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        function makeOrder() {
            if (!Object.keys(order).length) {
            alert('Please add items to your order first.');
            return;
            }
            alert('Order placed successfully! 🎉');
        }
    </script>
@endsection
