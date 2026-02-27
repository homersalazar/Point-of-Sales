<?php

namespace App\Repositories;

use App\Models\Purchase_order;
use Illuminate\Support\Facades\DB;

class PurchaseOrderRepository extends BaseRepository
{
    public function __construct(Purchase_order $model)
    {
        parent::__construct($model);
    }

    public function paginate($search = null, $perPage = 10)
    {
        return $this->model
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('suppliers.name', 'like', "%{$search}%")
                        ->orWhere('purchase_orders.po_number', 'like', "%{$search}%")
                        ->orWhere('purchase_orders.total_amount', 'like', "%{$search}%")
                        ->orWhere('purchase_orders.status', 'like', "%{$search}%");
                });
            })
            ->orderBy('purchase_orders.created_at', 'desc')
            ->select(
                'purchase_orders.*',
                'suppliers.name as supplier_name'
            )
            ->paginate($perPage)
            ->appends(['search' => $search]);
    }

    public function generatePONumber()
    {
        $year = date('Y');
        $lastOrder = $this->model->latest('id')->first();
        $lastId = $lastOrder ? $lastOrder->id : 0;

        return 'PO-' . $year . '-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
    }
}
