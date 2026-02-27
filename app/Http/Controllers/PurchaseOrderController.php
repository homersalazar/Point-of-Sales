<?php

namespace App\Http\Controllers;

use App\Models\Purchase_order;
use App\Services\ProductService;
use App\Services\PurchaseOrderService;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    protected $purchaseOrderService;
    protected $supplierService;
    protected $productService;

    public function __construct(
        PurchaseOrderService $purchaseOrderService,
        SupplierService $supplierService,
        ProductService $productService
    ) {
        $this->purchaseOrderService = $purchaseOrderService;
        $this->supplierService = $supplierService;
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 10);

        if ($perPage == -1) {
            $perPage = Purchase_order::count();
        }

        $purchaseOrders = $this->purchaseOrderService->paginate($search, $perPage);

        if ($request->ajax()) {
            return view('purchase_order.partials.purchase_order_table', compact('purchaseOrders'))->render();
        }

        return view('purchase_order.index', compact('purchaseOrders', 'search'));
    }

    public function create()
    {
        $suppliers = $this->supplierService->getAll();
        $products = $this->productService->getAll();
        return view('purchase_order.create_purchase_order', compact('suppliers', 'products'));
    }

    public function fetch_product(Request $request)
    {
        if ($request->ajax()) {

            $query = $request->get('query');

            if ($query) {

                $products = $this->productService->searchByName($query);

                $output = '<ul class="mt-1 md:w-[26rem] z-10 w-full text-sm border border-gray-300 text-gray-900 shadow-md rounded-lg p-2.5 hover:cursor-pointer">';

                if ($products->count() > 0) {

                    foreach ($products as $key => $product) {

                        $classes = 'cursor-pointer px-3 py-2 hover:bg-gray-200 transition-colors duration-200 ease-in-out';

                        if (isset($products[$key + 1])) {
                            $classes .= ' border-b';
                        }

                        $output .= '<li class="' . $classes . '"
                        onclick="add_purchase_order(\'' . $product->id . '\', \'' . e($product->name) . '\')">'
                            . ucwords($product->name) .
                            '</li>';
                    }
                } else {
                    $output .= '<li class="px-3 py-2 text-gray-500">Search Not Found</li>';
                }

                $output .= '</ul>';

                return $output;
            }
        }
    }

    public function store_purchase_order(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id'  => 'required|array',
            'quantity'    => 'required|array',
            'cost_price'  => 'required|array',
        ]);

        try {
            $poNumber = $this->purchaseOrderService->createPurchaseOrder($validated);

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order ' . $poNumber . ' created successfully!',
                'po_number' => $poNumber
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update_status(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:cancelled,completed',
        ]);

        $result = $this->purchaseOrderService->updateStatus($id, $request->action);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    public function items(Request $request)
    {
        $request->validate([
            'po_id' => 'required|exists:purchase_orders,id',
        ]);

        $items = $this->purchaseOrderService->getItems($request->po_id);
        $html = '';
        $count = 1;

        foreach ($items as $row) {
            $html .= '<tr class="bg-white border-b">';
            $html .= '<th class="p-2 text-left">' . $count . '</th>';
            $html .= '<td class="p-2 text-left">' . ucwords($row->product_name) . '</td>';
            $html .= '<td class="p-2 text-right">' . number_format($row->quantity, 2) . '</td>';
            $html .= '<td class="p-2 text-right">' . number_format($row->cost_price, 2) . '</td>';
            $html .= '<td class="p-2 text-right">' . number_format($row->subtotal, 2) . '</td>';
            $html .= '</tr>';

            $count++;
        }
        return $html;
    }
}
