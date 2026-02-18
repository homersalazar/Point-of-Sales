<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected $productService;
    protected $categoryService;

    public function __construct(ProductService $productService, CategoryService $categoryService)
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function sales_transaction(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $products = $this->productService->search($search, $perPage);
        $categories = $this->categoryService->getAll();

        return view('sale.sales_transaction', compact('products', 'search', 'perPage', 'categories'));
    }
}
