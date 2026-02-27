<?php

namespace App\Services;

use App\Repositories\PurchaseOrderRepository;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService extends BaseService
{
    protected $purchaseOrderRepo;

    public function __construct(PurchaseOrderRepository $purchaseOrderRepo)
    {
        parent::__construct($purchaseOrderRepo);
        $this->purchaseOrderRepo = $purchaseOrderRepo;
    }

    public function paginate($search = null, $perPage = 10)
    {
        return $this->purchaseOrderRepo->paginate($search, $perPage);
    }

    public function createPurchaseOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Generate PO number
            $poNumber = $this->purchaseOrderRepo->generatePONumber();

            // Insert purchase order
            $purchaseOrderId = DB::table('purchase_orders')->insertGetId([
                'po_number'   => $poNumber,
                'supplier_id' => $data['supplier_id'],
                'total_amount' => 0, // will calculate later
                'status'      => 'pending',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $totalAmount = 0;

            // Insert purchase items
            foreach ($data['product_id'] as $index => $productId) {
                $quantity = $data['quantity'][$index];
                $costPrice = $data['cost_price'][$index];
                $subtotal = $quantity * $costPrice;
                $totalAmount += $subtotal;

                DB::table('purchase_items')->insert([
                    'purchase_order_id' => $purchaseOrderId,
                    'product_id'        => $productId,
                    'quantity'          => $quantity,
                    'cost_price'        => $costPrice,
                    'subtotal'          => $subtotal,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }

            // Update total amount
            DB::table('purchase_orders')->where('id', $purchaseOrderId)->update([
                'total_amount' => $totalAmount
            ]);

            return $poNumber;
        });
    }

    public function updateStatus($id, $status): array
    {
        $updated = $this->update([
            'status' => $status
        ], $id);

        return $updated
            ? ['success' => true, 'message' => 'Purchase Order status updated successfully.']
            : ['success' => false, 'message' => 'Failed to update purchase order.'];
    }

    public function getItems($poId)
    {
        return DB::table('purchase_items')
            ->join('products', 'purchase_items.product_id', '=', 'products.id')
            ->where('purchase_items.purchase_order_id', $poId)
            ->select('products.name as product_name', 'purchase_items.quantity', 'purchase_items.cost_price', 'purchase_items.subtotal')
            ->get();
    }
}
