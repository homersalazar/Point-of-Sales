@extends('layouts.app')

@section('content')
    @include('expense.partials.create_expense_modal')
    @include('expense.partials.update_expense_modal')

    <div class="flex flex-col w-full space-y-6 p-4 sm:p-5">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">

            <!-- Left Side -->
            <div>
                <h1 class="text-2xl font-bold text-base-content">
                    Expense
                </h1>
                <p class="text-sm text-base-content/50 mt-1">
                    Manage your expense.
                </p>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3 w-full md:w-auto">

                <!-- Add Button -->
                <x-button
                    color="primary"
                    icon="fa-solid fa-plus"
                    click="add_expense"
                >
                Add Expense
            </x-button>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        <!-- Content Card -->
        <div class="bg-base-100 border border-base-200 rounded-2xl shadow-sm p-6">
            @include('expense.partials.expense_table')
        </div>
    </div>

@endsection
