<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\SaleService;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    protected $productService;
    protected $categoryService;
    protected $saleService;
    protected $customerService;

    public function __construct(
        ProductService  $productService,
        CategoryService $categoryService,
        SaleService     $saleService,
        CustomerService $customerService
    ) {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
        $this->saleService = $saleService;
        $this->customerService = $customerService;
    }

    public function sales_transaction(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $products = $this->productService->search($search, $perPage);
        $cats = $this->productService->countByCategory();
        $categories = $this->categoryService->getAll();
        $customers = $this->customerService->getAll();

        return view('sale.sales_transaction', compact('products', 'search', 'perPage', 'cats', 'categories', 'customers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'    => 'required|integer',
            'payment_method' => 'required|string',
            'items'          => 'required|array',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.price'  => 'required|numeric|min:0',
        ]);

        // Manually cast and merge in case validate strips it
        $data['customer_id'] = (int) $request->input('customer_id');

        $result = $this->saleService->placeOrder($data);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    public function sales_order(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);
        $sales = $this->saleService->getAllSales();

        return view('sale.sales_order', compact('search', 'perPage', 'sales'));
    }
}
