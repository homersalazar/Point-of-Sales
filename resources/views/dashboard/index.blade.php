@extends('layouts.app')

@section('content')
    @include('category.create_category_modal')
    @include('category.update_category_modal')

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
            <div class="stats shadow">
                <div class="stat">
                    <div class="stat-figure text-primary">
                        <i class="fa-solid fa-hand-holding-dollar text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Revenue</div>
                    <div class="stat-value text-primary">₱25.6K</div>
                    <div class="stat-desc">21% more than last month</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-secondary">
                        <i class="fa-brands fa-shopify text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Sales</div>
                    <div class="stat-value text-secondary">₱2.6M</div>
                    <div class="stat-desc">21% more than last month</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-accent">
                        <i class="fa-solid fa-file-invoice text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Expense</div>
                    <div class="stat-value text-accent">₱2.6k</div>
                    <div class="stat-desc">21% more than last month</div>
                </div>

                <div class="stat">
                    <div class="stat-figure text-neutral">
                        <i class="fa-solid fa-cart-arrow-down text-4xl"></i>
                    </div>
                    <div class="stat-title">Total Purchase</div>
                    <div class="stat-value text-neutral">₱2.6k</div>
                    <div class="stat-desc">21% more than last month</div>
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
                        <table class="table">
                            <!-- head -->
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- row 1 -->
                                <tr>
                                    <td>Cy Ganderton</td>
                                    <td>3</td>
                                </tr>
                                <!-- row 2 -->
                                <tr>
                                    <td>Hart Hagerty</td>
                                    <td>4</td>
                                </tr>
                                <!-- row 3 -->
                                <tr>
                                    <td>Brice Swyre</td>
                                    <td>4</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [
                    {
                        label: 'Sales',
                        data: [120, 150, 180, 200, 250, 300, 320, 350, 400, 420, 450, 500],
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        tension: 0.4
                    },
                    {
                        label: 'Purchases',
                        data: [100, 120, 150, 180, 200, 230, 250, 280, 300, 320, 350, 380],
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
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Sales vs Purchases'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
@endsection
