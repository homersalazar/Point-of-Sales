@extends('layouts.app')

@section('content')
    @include('product.partials.create_product_modal')
    @include('product.partials.update_product_modal')

    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    Products
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Manage your products, pricing, and stock levels.
                </p>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3 w-full md:w-auto">

                <!-- Search -->
                <div class="w-full md:w-72">
                    <x-search-input
                        url="{{ route('product.index') }}"
                        placeholder="Search products"
                        target="productTable"
                    />
                </div>

                <!-- Add Button -->
                <x-button
                    color="primary"
                    icon="fa-solid fa-plus"
                    click="add_product"
                >
                    Add Product
                </x-button>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        <!-- Content Card -->
        <div class="bg-base-100 border border-base-200 rounded-2xl shadow-sm p-6">
            @if ($products->isEmpty())

                <!-- Example Empty State -->
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-full bg-base-200 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-box text-base-content/40 text-xl"></i>
                    </div>
                    <h2 class="font-semibold text-base-content text-lg">
                        No Products Found
                    </h2>
                    <p class="text-sm text-base-content/50 mt-1 mb-4">
                        Start by adding your first product.
                    </p>

                    <x-button
                        color="primary"
                        click="add_product"
                    >
                        Add Product
                    </x-button>
                </div>
            @else
                <x-entries />
                @include('product.partials.product_table', ['products' => $products])
            @endif
        </div>
    </div>

@endsection
