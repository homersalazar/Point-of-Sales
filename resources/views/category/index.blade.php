@extends('layouts.app')

@section('content')
    @include('category.partials.create_category_modal')
    @include('category.partials.update_category_modal')

    <div class="flex flex-col w-full space-y-4 sm:space-y-6 px-4 sm:px-5">

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <!-- Left Side -->
            <div class="min-w-0">
                <h1 class="text-xl sm:text-2xl font-bold text-base-content truncate">
                    Categories
                </h1>
                <p class="text-xs sm:text-sm text-base-content/50 mt-1">
                    Manage your categories.
                </p>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3 w-full sm:w-auto shrink-0">

                <!-- Add Button -->
                <x-button
                    class="w-full sm:w-auto justify-center"
                    color="primary"
                    icon="fa-solid fa-plus"
                    click="add_category"
                >
                    Add Category
                </x-button>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider my-0"></div>

        <!-- Content Card -->
        <div class="bg-base-100 border border-base-200 rounded-xl sm:rounded-2xl shadow-sm p-3 sm:p-6 overflow-x-auto">
            @include('category.partials.category_table')
        </div>
    </div>

@endsection
