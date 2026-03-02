@extends('layouts.app')

@section('content')
    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    Dashboard Overview
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Let's check your store today
                </p>
            </div>


            <!-- Right Side -->
            {{-- <div class="flex items-center gap-3 w-full md:w-auto">

                <!-- Search -->
                <div class="w-full md:w-72">
                    <x-search-input
                        url="{{ route('category.index') }}"
                        placeholder="Search categories"
                        target="categoryTable"
                    />
                </div>

                <!-- Add Button -->
                <x-button
                    color="primary"
                    icon="fa-solid fa-plus"
                    click="add_category"
                >
                    Add Category
                </x-button>
            </div> --}}
        </div>

        <div class="flex flex-col gap-5 w-full">
            <div class="stats shadow w-full">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fa-solid fa-hand-holding-dollar text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Revenue</div>
                    <div class="stat-value text-primary">₱{{ $total_revenue }}</div>
                    <div class="stat-desc">
                        @if($revenuePercentageChange >= 0)
                            <span class="text-green-600 font-semibold">
                                <i class="fa-solid fa-arrow-trend-up"></i> {{ $revenuePercentageChange }}% more than last month
                            </span>
                        @else
                            <span class="text-red-600 font-semibold">
                                <i class="fa-solid fa-arrow-trend-down"></i> {{ abs($revenuePercentageChange) }}% less than last month
                            </span>
                        @endif
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <i class="fa-brands fa-shopify text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Sales</div>
                    <div class="stat-value text-secondary">₱{{ $total_sales }}</div>
                    <div class="stat-desc">
                        @if($salesPercentageChange >= 0)
                            <span class="text-green-600 font-semibold">
                                <i class="fa-solid fa-arrow-trend-up"></i> {{ $salesPercentageChange }}% more than last month
                            </span>
                        @else
                            <span class="text-red-600 font-semibold">
                                <i class="fa-solid fa-arrow-trend-down"></i> {{ abs($salesPercentageChange) }}% less than last month
                            </span>
                        @endif
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-accent">
                        <i class="fa-solid fa-file-invoice text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Expense</div>
                    <div class="stat-value text-accent">₱{{ $total_expenses }}</div>
                    <div class="stat-desc">
                        @if($expensesPercentageChange >= 0)
                            <span class="text-green-600 font-semibold">
                                <i class="fa-solid fa-arrow-trend-up"></i> {{ $expensesPercentageChange }}% more than last month
                            </span>
                        @else
                            <span class="text-red-600 font-semibold">
                                <i class="fa-solid fa-arrow-trend-down"></i> {{ abs($expensesPercentageChange) }}% less than last month
                            </span>
                        @endif
                    </div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-neutral">
                        <i class="fa-solid fa-cart-arrow-down text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Purchase</div>
                    <div class="stat-value text-neutral">₱{{ $total_purchases }}</div>
                    <div class="stat-desc">
                        @if($purchasesPercentageChange >= 0)
                            <span class="text-green-600 font-semibold">
                                <i class="fa-solid fa-arrow-trend-up"></i> {{ $purchasesPercentageChange }}% more than last month
                            </span>
                        @else
                            <span class="text-red-600 font-semibold">
                                <i class="fa-solid fa-arrow-trend-down"></i> {{ abs($purchasesPercentageChange) }}% less than last month
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex flex-row gap-5 w-full">
                <div class="bg-white rounded-xl p-5 w-3/4">
                    <h1 class="font-bold text-xl">Sales & Purchases</h1>
                    <canvas id="myChart"></canvas>
                </div>
                <div class="bg-white rounded-xl p-5 w-1/4">
                    <h1 class="font-bold text-lg">Stock Alert</h1>
                    <div class="overflow-x-auto w-full">
                        <x-table :headers="['Product', 'Quantity']">
                            @foreach ($lowStockProducts as $alert)
                                <tr>
                                    <td>{{ $alert->name }}</td>
                                    <td>
                                        @php
                                            $color = 'bg-gray-100 text-gray-700';

                                            if ($alert->stock == 0) {
                                                $color = 'bg-red-100 text-red-700';
                                            } elseif ($alert->stock <= 5) {
                                                $color = 'bg-yellow-100 text-yellow-700';
                                            } elseif ($alert->stock >= 10) {
                                                $color = 'bg-green-100 text-green-700';
                                            }
                                        @endphp

                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $color }}">
                                            {{ $alert->stock }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </x-table>

                        @if ($lowStockProducts instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <div class="mt-4">
                                {{ $lowStockProducts->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const ctx = document.getElementById('myChart');

        const sales = @json($monthlyTotalSales);
        const purchases = @json($monthlyTotalPurchases);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Sales',
                        data: sales,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Purchases',
                        data: purchases,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Sales vs Purchases'
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection
