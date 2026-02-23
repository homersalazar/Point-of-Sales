<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
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
