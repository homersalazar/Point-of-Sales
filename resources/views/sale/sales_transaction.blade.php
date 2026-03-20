@extends('layouts.app')

@section('content')
    @include('product.partials.create_product_modal')

    <div class="flex flex-col lg:flex-row h-screen bg-base-200 overflow-hidden gap-0">

        {{-- ══════════════ LEFT PANEL ══════════════ --}}
        <div id="leftPanel" class="flex flex-col flex-1 overflow-hidden p-4 lg:p-5">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4 w-full">
                <!-- Left: Title -->
                <div class="flex flex-col w-full">
                    <h1 class="text-xl lg:text-2xl font-bold text-base-content leading-tight">
                        Sales Transaction
                    </h1>
                    <p class="text-xs lg:text-sm text-base-content/50 mt-0.5 md:mt-0">
                        {{ \Carbon\Carbon::now()->format('F d, Y') }}
                    </p>
                </div>

                <!-- Right: Search + Order -->
                <div class="flex flex-row items-center gap-2 w-full md:w-auto">
                    <!-- Search -->
                    <label class="input input-bordered input-sm flex items-center gap-2 w-full md:w-auto">
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search..."
                            class="w-full md:w-64"
                            oninput="filterProducts()"
                        />
                        <i class="fa-solid fa-magnifying-glass w-4 h-4"></i>
                    </label>

                    <!-- Order Button -->
                    <button
                        onclick="openOrderDrawer()"
                        class="lg:hidden btn btn-primary btn-sm gap-1.5 relative ml-2"
                    >
                        <i class="fa-solid fa-receipt"></i>
                        <span class="hidden sm:inline">Order</span>
                        <span
                            id="cartBadge"
                            class="hidden absolute -top-1.5 -right-1.5 w-4 h-4 rounded-full bg-error text-white text-[10px] font-bold flex items-center justify-center"
                        >0</span>
                    </button>
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="flex gap-2 mb-4 overflow-x-auto pb-1 scrollbar-none">
                @foreach($cats as $cat)
                    <button
                        onclick="setCategory(this, '{{ $cat->id }}')"
                        data-cat="{{ $cat->id }}"
                        class="cat-tab btn btn-ghost flex-shrink-0 w-28 lg:w-32 h-auto py-2 px-3 flex-col items-start rounded-xl border border-base-300 bg-white normal-case hover:bg-yellow-50 hover:border-yellow-400 transition-all {{ $cat->id === 0 ? '!bg-yellow-500 !border-yellow-500 text-white hover:!bg-yellow-600' : '' }}">
                        <span class="font-bold text-xs lg:text-sm {{ $cat->id === 0 ? 'text-white' : 'text-base-content' }}">{{ $cat->name }}</span>
                        <span class="text-xs font-normal mt-0.5 {{ $cat->id === 0 ? 'text-white/70' : 'text-base-content/50' }}">{{ $cat->total_products }}</span>
                    </button>
                @endforeach
            </div>

            {{-- Product Grid --}}
            <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3 overflow-y-auto scrollbar-thin pr-1 auto-rows-fr">
                {{-- Add New Product Card --}}
                <x-button
                    click="add_product"
                    class="flex flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-yellow-200 bg-yellow-50/60 cursor-pointer hover:bg-yellow-100 hover:border-yellow-400 transition-all min-h-[140px] lg:min-h-[160px] group h-40 lg:h-44 w-full">
                    <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-full bg-white shadow flex items-center justify-center text-yellow-500 text-2xl font-light group-hover:scale-110 transition-transform">
                        +
                    </div>
                    <span class="text-xs lg:text-sm font-semibold text-yellow-500">Add New Product</span>
                </x-button>

                @foreach($products as $row)
                    <div class="product-card group relative bg-white rounded-2xl border border-base-200
                        hover:shadow-xl hover:-translate-y-1 transition-transform cursor-pointer
                        overflow-hidden"
                        data-id="{{ $row->id }}"
                        data-name="{{ $row->name }}"
                        data-price="{{ $row->selling_price }}"
                        data-img="{{ $row->image }}"
                        data-category="{{ $row->category_id }}"
                        onclick="addToOrder(this)"
                    >
                        <figure class="h-24 lg:h-28 overflow-hidden">
                            <img src="{{ asset('storage/product/' . $row->image) }}"
                                alt="{{ $row->name }}"
                                class="h-full w-full object-cover group-hover:scale-110 transition duration-300"
                            />
                        </figure>
                        <div class="p-2 lg:p-2.5">
                            <h2 class="text-sm font-semibold truncate">{{ $row->name }}</h2>
                            <p class="text-xs lg:text-sm font-bold text-primary">₱ {{ number_format($row->selling_price,2) }}</p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- ══════════════ RIGHT PANEL (desktop sidebar) ══════════════ --}}
        <div class="hidden lg:flex w-[300px] xl:w-[320px] flex-shrink-0 border-l border-yellow-200 px-5 py-5 flex-col bg-base-100 h-full">
            @include('sale.partials.order_panel')
        </div>

        {{-- ══════════════ MOBILE DRAWER OVERLAY ══════════════ --}}
        <div
            id="drawerOverlay"
            onclick="closeOrderDrawer()"
            class="lg:hidden fixed inset-0 bg-black/40 z-40 hidden opacity-0 transition-opacity duration-300"
        ></div>

        {{-- ══════════════ MOBILE DRAWER ══════════════ --}}
        <div
            id="orderDrawer"
            class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-base-100 rounded-t-3xl shadow-2xl border-t border-yellow-200 flex flex-col overflow-hidden transition-transform duration-300 translate-y-full"
            style="max-height: 90dvh;"
        >
            <!-- Drawer Handle -->
            <div class="flex items-center justify-between px-5 pt-4 pb-2 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-1 rounded-full bg-base-300 absolute left-1/2 -translate-x-1/2 top-3"></div>
                    <h2 class="text-lg font-extrabold text-base-content">Detail Order</h2>
                </div>
                <button onclick="closeOrderDrawer()" class="btn btn-ghost btn-sm btn-circle">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-5 pb-5">
                @include('sale.partials.order_panel')
            </div>
        </div>

    </div>

    <script>
        const order = {};

        function fmt(n) {
            return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // ── Drawer ──────────────────────────────────────────
        function openOrderDrawer() {
            const drawer = document.getElementById('orderDrawer');
            const overlay = document.getElementById('drawerOverlay');
            overlay.classList.remove('hidden');
            requestAnimationFrame(() => {
                overlay.classList.remove('opacity-0');
                drawer.classList.remove('translate-y-full');
            });
        }

        function closeOrderDrawer() {
            const drawer = document.getElementById('orderDrawer');
            const overlay = document.getElementById('drawerOverlay');
            drawer.classList.add('translate-y-full');
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }

        function updateCartBadge() {
            const count = Object.keys(order).length;
            const badge = document.getElementById('cartBadge');
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
                badge.classList.add('flex');
            } else {
                badge.classList.add('hidden');
                badge.classList.remove('flex');
            }
        }

        // ── Order Logic ──────────────────────────────────────
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
            updateCartBadge();
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
            updateCartBadge();
        }

        function removeItem(id) {
            delete order[id];
            const card = document.querySelector(`.product-card[data-id="${id}"]`);
            if (card) card.classList.remove('!border-base-500', 'ring-2', 'ring-primary');
            renderOrder();
            updateCartBadge();
        }

        function renderOrder() {
            // Update all order containers (desktop + mobile drawer)
            const containers = document.querySelectorAll('.order-items-container');
            const ids = Object.keys(order);
            const assetBase = "{{ asset('storage/product') }}";

            let subtotal = 0;
            let html = '';

            if (ids.length === 0) {
                html = '<p class="text-sm text-base-content/40 text-center py-8">No items added yet.</p>';
            } else {
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
            }

            containers.forEach(c => c.innerHTML = html);

            document.querySelectorAll('.order-item-count').forEach(el => el.textContent = ids.length);
            document.querySelectorAll('.order-subtotal').forEach(el => el.textContent = fmt(subtotal));
            document.querySelectorAll('.order-total').forEach(el => el.textContent = fmt(subtotal));
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

        function computeChange() {
            document.querySelectorAll('.order-total').forEach(el => {
                const total = parseFloat(el.textContent.replace(/[₱,]/g, '')) || 0;
                const received = parseFloat(document.querySelector('.amount-received-input')?.value) || 0;
                const change = received - total;
                document.querySelectorAll('.change-amount-input').forEach(input => {
                    if (change >= 0) {
                        input.value = fmt(change);
                        input.classList.remove('text-red-600');
                        input.classList.add('text-green-600');
                    } else {
                        input.value = 'Insufficient amount';
                        input.classList.remove('text-green-600');
                        input.classList.add('text-red-600');
                    }
                });
            });
        }

        function togglePaymentMethod() {
            const method = document.querySelector('select[name="payment_method"]').value;
            document.querySelectorAll('.cash-fields').forEach(el => {
                el.classList.toggle('hidden', method === 'gcash');
            });

            if (method === 'gcash') {
                const totalText = document.querySelector('.order-total')?.textContent || '0';
                const total = parseFloat(totalText.replace(/[₱,]/g, '')) || 0;
                document.querySelectorAll('.amount-received-input').forEach(i => i.value = total);
                document.querySelectorAll('.change-amount-input').forEach(i => {
                    i.value = fmt(0);
                    i.classList.remove('text-red-600');
                    i.classList.add('text-green-600');
                });
            } else {
                document.querySelectorAll('.amount-received-input').forEach(i => i.value = '');
                document.querySelectorAll('.change-amount-input').forEach(i => i.value = '');
            }
        }

        function confirmOrder() {
            const totalText = document.querySelector('.order-total')?.textContent || '0';
            const total = parseFloat(totalText.replace(/[₱,]/g, '')) || 0;
            const received = parseFloat(document.querySelector('.amount-received-input')?.value) || 0;

            if (received < total) {
                Swal.fire({ title: 'Insufficient Payment', text: 'Amount received is less than total payment.', icon: 'warning', timer: 3000, showConfirmButton: false });
                return;
            }

            const customer_id = document.querySelector('select[name="customer_id"]').value;
            const payment_method = document.querySelector('select[name="payment_method"]').value;

            $.ajax({
                url: "{{ route('sale.store') }}",
                method: "POST",
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                data: JSON.stringify({
                    customer_id, payment_method,
                    amount_received: received,
                    change: received - total,
                    items: Object.values(order),
                }),
                success: function(data) {
                    Swal.fire({ title: 'Success!', text: data.message, icon: 'success', timer: 3000, showConfirmButton: false })
                        .then(() => {
                            Object.keys(order).forEach(id => delete order[id]);
                            renderOrder();
                            updateCartBadge();
                            document.querySelectorAll('.amount-received-input').forEach(i => i.value = '');
                            document.querySelectorAll('.change-amount-input').forEach(i => i.value = '');
                            closeOrderDrawer();
                        });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', togglePaymentMethod);
    </script>
@endsection
