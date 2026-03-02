@extends('layouts.app')

@section('content')
    @include('purchase_order.partials.view_purchase_order_modal')

    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    Purchase Orders
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Manage your purchase orders.
                </p>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3 w-full md:w-auto">

                <!-- Search -->
                <div class="w-full md:w-72">
                    <x-search-input
                        url="{{ route('purchase_order.index') }}"
                        placeholder="Search purchase orders"
                        target="purchaseOrderTable"
                    />
                </div>

                <!-- Add Button -->
                <a href="{{ route('purchase_order.create') }}"
                    class="btn btn-primary btn-sm"
                >
                    <i class="fa fa-plus"></i>
                    Add Purchase Order
                </a>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        <!-- Content Card -->
        <div class="bg-base-100 border border-base-200 rounded-2xl shadow-sm p-6">
            @if ($purchaseOrders->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-full bg-base-200 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-box text-base-content/40 text-xl"></i>
                    </div>
                    <h2 class="font-semibold text-base-content text-lg">
                        No Purchase Order Found
                    </h2>
                    <p class="text-sm text-base-content/50 mt-1 mb-4">
                        Start by adding your first purchase order.
                    </p>

                    <a href="{{ route('purchase_order.create') }}"
                        type="button"
                        class="btn btn-primary btn-sm"
                    >
                        Add Purchase Order
                    </a>

                </div>
            @else
                <x-entries />
                @include('purchase_order.partials.purchase_order_table', ['purchaseOrders' => $purchaseOrders])
            @endif
        </div>
    </div>

@endsection
