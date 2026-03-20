@extends('layouts.app')

@section('content')
    @include('purchase_order.partials.view_purchase_order_modal')

    <div class="flex flex-col w-full space-y-6 p-4 sm:p-5">

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
            @include('purchase_order.partials.purchase_order_table')
        </div>
    </div>

@endsection
