@extends('layouts.app')

@section('content')
    @include('expense.partials.create_exp_category_modal')
    @include('expense.partials.update_exp_category_modal')

    <div class="flex flex-col w-full space-y-6">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    Expense Categories
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Manage your expense categories.
                </p>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3 w-full md:w-auto">

                <!-- Search -->
                <div class="w-full md:w-72">
                    <x-search-input
                        url="{{ route('expense.exp_category') }}"
                        placeholder="Search categories"
                        target="expenseCategoryTable"
                    />
                </div>

                <!-- Add Button -->
                <x-button
                    color="primary"
                    icon="fa-solid fa-plus"
                    click="add_exp_category"
                >
                Add Exp. Category
            </x-button>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        <!-- Content Card -->
        <div class="bg-base-100 border border-base-200 rounded-2xl shadow-sm p-6">
            @if ($exp_categories->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-full bg-base-200 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-box text-base-content/40 text-xl"></i>
                    </div>
                    <h2 class="font-semibold text-base-content text-lg">
                        No Category Found
                    </h2>
                    <p class="text-sm text-base-content/50 mt-1 mb-4">
                        Start by adding your first category.
                    </p>

                    <x-button color="primary" click="add_exp_category">
                        Add Expense Category
                    </x-button>
                </div>
            @else
                <x-entries />
                @include('expense.partials.expense_category_table', ['exp_categories' => $exp_categories])
            @endif
        </div>
    </div>

@endsection
