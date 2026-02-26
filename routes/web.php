<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
});

Route::prefix('product')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('product.index');
    Route::post('/create_product', [ProductController::class, 'create_product'])->name('product.create_product');
    Route::put('/update_product/{id}', [ProductController::class, 'update_product'])->name('product.update_product');
    Route::delete('/delete_product/{id}', [ProductController::class, 'delete_product'])->name('product.delete_product');
});

Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/create_category', [CategoryController::class, 'create_category'])->name('category.create_category');
    Route::put('/update_category/{id}', [CategoryController::class, 'update_category'])->name('category.update_category');
    Route::delete('/delete_category/{id}', [CategoryController::class, 'delete_category'])->name('category.delete_category');
});


Route::prefix('sale')->group(function () {
    Route::get('/', [SaleController::class, 'sales_transaction'])->name('sale.sales_transaction');
    Route::post('/store', [SaleController::class, 'store'])->name('sale.store');

    Route::get('/sales_order', [SaleController::class, 'sales_order'])->name('sale.sales_order');
    Route::put('/update/{id}', [SaleController::class, 'update'])->name('sale.update');
});

Route::prefix('customer')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customer.index');
    Route::post('/create_customer', [CustomerController::class, 'create_customer'])->name('customer.create_customer');
    Route::put('/update_customer/{id}', [CustomerController::class, 'update_customer'])->name('customer.update_customer');
    Route::delete('/delete_customer/{id}', [CustomerController::class, 'delete_customer'])->name('customer.delete_customer');
});

Route::prefix('supplier')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('supplier.index');
    Route::post('/create_supplier', [SupplierController::class, 'create_supplier'])->name('supplier.create_supplier');
    Route::put('/update_supplier/{id}', [SupplierController::class, 'update_supplier'])->name('supplier.update_supplier');
    Route::delete('/delete_supplier/{id}', [SupplierController::class, 'delete_supplier'])->name('supplier.delete_supplier');
});

Route::prefix('expense')->group(function () {
    Route::get('/', [ExpenseController::class, 'index'])->name('expense.index');
    Route::post('/create_expense', [ExpenseController::class, 'store'])->name('expense.create_expense');
    Route::put('/update/{id}', [ExpenseController::class, 'update'])->name('expense.update');
    Route::put('/update_status/{id}', [ExpenseController::class, 'update_status'])->name('expense.update_status');
    Route::delete('/delete_expense/{id}', [ExpenseController::class, 'delete_expense'])->name('expense.delete_expense');

    Route::get('/exp_category', [ExpenseController::class, 'exp_category'])->name('expense.exp_category');
    Route::post('/create_exp_category', [ExpenseController::class, 'create_exp_category'])->name('expense.create_exp_category');
    Route::put('/update_exp_category/{id}', [ExpenseController::class, 'update_exp_category'])->name('expense.update_exp_category');
    Route::delete('/delete_expense_category/{id}', [ExpenseController::class, 'delete_expense_category'])->name('expense.delete_expense_category');
});
