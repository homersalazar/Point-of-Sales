<?php

namespace App\Repositories;

use App\Models\Purchase_order;
use Carbon\Carbon;

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

    public function totalPurchaseOrders($startdate = null, $enddate = null)
    {
        $start = $startdate
            ? Carbon::parse($startdate)->startOfDay()
            : Carbon::now()->startOfMonth();   // ✅ start of current month

        $end = $enddate
            ? Carbon::parse($enddate)->endOfDay()
            : Carbon::now()->endOfMonth();     // ✅ end of current month

        return Purchase_order::whereBetween('created_at', [$start, $end])
                    ->where('status', 'completed')
                    ->sum('total_amount');
    }

    public function totalPurchaseOrdersLastMonth()
    {
        return Purchase_order::whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->where('status', 'completed')
            ->sum('total_amount');
    }

    public function monthlyPurchases($year = null)
    {
        $year = $year ?? now()->year;

        return Purchase_order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                    ->whereYear('created_at', $year)
                    ->where('status', 'completed')
                    ->groupBy('month')
                    ->pluck('total', 'month')
                    ->toArray();
    }
}
