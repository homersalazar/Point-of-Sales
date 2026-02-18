<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
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
});

Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('category.index');
    Route::post('/create_category', [CategoryController::class, 'create_category'])->name('category.create_category');
    Route::put('/update_category/{id}', [CategoryController::class, 'update_category'])->name('category.update_category');
    Route::delete('/delete_category/{id}', [CategoryController::class, 'delete_category'])->name('category.delete_category');
});
