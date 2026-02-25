<!DOCTYPE html>
    <html lang="en" data-theme="bumblebee">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Point of Sales</title>
        {{-- JQuery CDN --}}
        <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>

        {{-- Daisy UI --}}
        <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" />

        {{-- Tailwind CSS --}}
        <script src="https://cdn.tailwindcss.com"></script>

        {{-- Font Awesome --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet" />

        {{-- Sweet Alert 2 --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.2/dist/sweetalert2.min.css">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.2/dist/sweetalert2.all.min.js"></script>

        {{-- Graph Js --}}
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    </head>
        <body class="bg-base-200 min-h-screen flex">

        <!-- ══════════ SIDEBAR ══════════ -->
        <aside id="sidebar" class="sidebar bg-base-100 border-r border-base-200 flex flex-col h-screen sticky top-0 shadow-sm">

            <!-- Header -->
            <div class="flex items-center justify-between px-4 py-[14px] border-b border-base-200 gap-2">
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-bolt text-primary-content" style="font-size:14px"></i>
                    </div>
                    <span class="logo-text font-semibold text-base-content text-[15px]">Menu</span>
                </div>
                <button onclick="toggleSidebar()" class="toggle-btn w-7 h-7 rounded-lg hover:flex items-center justify-center transition-colors flex-shrink-0">
                    <i class="fa-solid fa-angles-left text-base-content/40" style="font-size:13px"></i>
                </button>
            </div>

            <!-- Nav -->
            <nav class="flex-1 py-3 px-2 space-y-0.5">

                <!-- Dashboard -->
                <a href="/dashboard" class="nav-item" data-tip="Dashboard">
                    <i class="fa-solid fa-gauge-high nav-icon"></i>
                    <span class="nav-label">Dashboard</span>
                </a>

                <!-- Inventory -->
                <div>
                    <div class="nav-item" data-tip="Inventory" onclick="toggleMenu('inventory')">
                        <i class="fa-solid fa-boxes-stacked nav-icon"></i>
                        <span class="nav-label" style="flex:1">Inventory</span>
                        <i class="fa-solid fa-chevron-down caret-icon text-base-content/30" id="caret-inventory"></i>
                    </div>
                    <div id="submenu-inventory" class="submenu closed pl-3 mt-0.5 space-y-0.5">
                        <a href="/product" class="nav-item" data-tip="Products">
                            <i class="fa-solid fa-box nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Products</span>
                        </a>
                        <a href="/category" class="nav-item" data-tip="Categories">
                            <i class="fa-solid fa-tag nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Categories</span>
                        </a>
                    </div>
                </div>

                <!-- Sales -->
                <div>
                    <div class="nav-item" data-tip="Sales" onclick="toggleMenu('sales')">
                        <i class="fa-solid fa-bag-shopping nav-icon"></i>
                        <span class="nav-label" style="flex:1">Sales</span>
                        <i class="fa-solid fa-chevron-down caret-icon open text-base-content/30" id="caret-sales"></i>
                    </div>
                    <div id="submenu-sales" class="submenu pl-3 mt-0.5 space-y-0.5">
                        <a href="/sale" class="nav-item" data-tip="Sales Transaction">
                            <i class="fa-solid fa-cart-shopping nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Sales Transaction</span>
                        </a>
                        <a href="/sale/sales_order" class="nav-item" data-tip="Sales Orders">
                            <i class="fa-solid fa-clipboard-list nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Sales Orders</span>
                        </a>
                        <div class="nav-item" data-tip="Invoice">
                            <i class="fa-solid fa-file-invoice nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Invoice</span>
                        </div>
                        <a href="/customer/" class="nav-item" data-tip="Customers">
                            <i class="fa-solid fa-users nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Customers</span>
                        </a>
                    </div>
                </div>

                <!-- Purchase -->
                <div>
                    <div class="nav-item" data-tip="Purchase" onclick="toggleMenu('purchase')">
                        <i class="fa-solid fa-basket-shopping nav-icon"></i>
                        <span class="nav-label" style="flex:1">Purchase</span>
                        <i class="fa-solid fa-chevron-down caret-icon text-base-content/30" id="caret-purchase"></i>
                    </div>
                    <div id="submenu-purchase" class="submenu closed pl-3 mt-0.5 space-y-0.5">
                        <div class="nav-item" data-tip="Purchase Orders">
                            <i class="fa-solid fa-file-lines nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Purchase Orders</span>
                        </div>
                        <div class="nav-item" data-tip="Suppliers">
                            <i class="fa-solid fa-truck nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Suppliers</span>
                        </div>
                    </div>
                </div>

                <!-- Customer Portal -->
                <div>
                    <div class="nav-item" data-tip="Customer Portal" onclick="toggleMenu('portal')">
                        <i class="fa-solid fa-circle-user nav-icon"></i>
                        <span class="nav-label" style="flex:1">Customer Portal</span>
                        <i class="fa-solid fa-chevron-down caret-icon text-base-content/30" id="caret-portal"></i>
                    </div>
                    <div id="submenu-portal" class="submenu closed pl-3 mt-0.5 space-y-0.5">
                        <div class="nav-item" data-tip="Portal Access">
                            <i class="fa-solid fa-right-to-bracket nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Portal Access</span>
                        </div>
                    </div>
                </div>

                <!-- Expense -->
                <div>
                    <div class="nav-item" data-tip="Expenses" onclick="toggleMenu('expenses')">
                        <i class="fa-solid fa-receipt nav-icon"></i>
                        <span class="nav-label" style="flex:1">Expenses</span>
                        <i class="fa-solid fa-chevron-down caret-icon text-base-content/30" id="caret-expenses"></i>
                    </div>
                    <div id="submenu-expenses" class="submenu closed pl-3 mt-0.5 space-y-0.5">
                        <a href="/expense/exp_category" class="nav-item" data-tip="Expense Transactions">
                            <i class="fa-solid fa-file-invoice nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Expense Transactions</span>
                        </a>
                        <a href="/expense/exp_category" class="nav-item" data-tip="Expense Categories">
                            <i class="fa-solid fa-tag nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Expense Categories</span>
                        </a>
                    </div>
                </div>

                <!-- Report -->
                <div>
                    <div class="nav-item" data-tip="Report" onclick="toggleMenu('report')">
                        <i class="fa-solid fa-chart-bar nav-icon"></i>
                        <span class="nav-label" style="flex:1">Report</span>
                        <i class="fa-solid fa-chevron-down caret-icon text-base-content/30" id="caret-report"></i>
                    </div>
                    <div id="submenu-report" class="submenu closed pl-3 mt-0.5 space-y-0.5">
                        <div class="nav-item" data-tip="Sales Report">
                            <i class="fa-solid fa-chart-line nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Sales Report</span>
                            </div>
                        <div class="nav-item" data-tip="Analytics">
                            <i class="fa-solid fa-chart-pie nav-icon" style="font-size:13px"></i>
                            <span class="nav-label">Analytics</span>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Bottom -->
            <div class="border-t border-base-200 p-2 space-y-0.5">
                <div class="nav-item" data-tip="Settings">
                    <i class="fa-solid fa-gear nav-icon"></i>
                    <span class="nav-label">Settings</span>
                </div>
                <div class="nav-item" data-tip="Log Out">
                    <i class="fa-solid fa-arrow-right-from-bracket nav-icon"></i>
                    <span class="nav-label">Log out</span>
                </div>

                <!-- User profile -->
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-btn hover:bg-base-200 cursor-pointer transition-colors mt-0.5">
                    <div class="avatar placeholder flex-shrink-0">
                        <div class="bg-primary text-primary-content rounded-full w-8">
                        <span class="text-xs font-bold">HD</span>
                        </div>
                    </div>
                    <div class="user-info overflow-hidden leading-tight">
                        <p class="text-sm font-semibold text-base-content truncate">Hanin Dhiya</p>
                        <p class="text-xs text-base-content/50">Admin</p>
                    </div>
                </div>
            </div>

        </aside>

        <!-- ══════════ MAIN CONTENT ══════════ -->
        <main class="flex-1 p-5">
            @yield('content')
        </main>

        @if (session('success'))
            <script>
                Swal.fire({
                    title: "Success!",
                    text: "{{ session('success') }}",
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2500
                });
            </script>
        @elseif (session('error'))
            <script>
                Swal.fire({
                    title: "Error!",
                    text: "{{ session('error') }}",
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2500
                });
            </script>
        @elseif ($errors->any())
            <script>
                Swal.fire({
                    title: "Error!",
                    text: "{{ $errors->first() }}",
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2500
                });
            </script>
        @elseif (session('info'))
            <script>
                Swal.fire({
                    title: "Info!",
                    text: "{{ session('info') }}",
                    icon: 'info',
                    showConfirmButton: false,
                    timer: 2500
                });
            </script>
        @endif

        <script>
            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('collapsed');
            }

            function toggleMenu(name) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar.classList.contains('collapsed')) return;
                const submenu = document.getElementById('submenu-' + name);
                const caret   = document.getElementById('caret-' + name);
                const isOpen  = !submenu.classList.contains('closed');
                submenu.classList.toggle('closed', isOpen);
                caret.classList.toggle('open', !isOpen);
            }

        </script>
    </body>
</html>
