<?php

namespace App\Repositories;

use App\Models\Purchase_order;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

    public function getPurchaseOrderData(Request $request)
    {
        $columns = ['purchase_orders.po_number', 'suppliers.name', 'purchase_orders.total_amount', 'purchase_orders.status', 'action'];

        $totalData = DB::table('purchase_orders')->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('purchase_orders')
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select('purchase_orders.id', 'purchase_orders.po_number', 'purchase_orders.total_amount', 'purchase_orders.status', DB::raw('COALESCE(suppliers.name, "") as vendor'));

        if (!empty($search = $request->input('search.value'))) {
            $query->where('purchase_orders.po_number', 'LIKE', "%{$search}%")
                ->orWhere('purchase_orders.total_amount', 'LIKE', "%{$search}%")
                ->orWhere('purchase_orders.status', 'LIKE', "%{$search}%")
                ->orWhere('suppliers.name', 'LIKE', "%{$search}%");
        }

        $totalFiltered = (clone $query)->count();

        $users = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];

        foreach ($users as $row) {
            $classes = match($row->status) {
                'pending'   => 'bg-yellow-100 text-yellow-700',
                'cancelled' => 'bg-red-100 text-red-700',
                'completed' => 'bg-green-100 text-green-700',
                default     => 'bg-gray-100 text-gray-700',
            };

            $status = '<span class="px-3 py-1 rounded-lg text-center font-semibold text-sm ' . $classes . '">
                            ' . ucwords($row->status) . '
                        </span>';

            $action = '<div class="flex gap-2">';

                if ($row->status === 'pending') {
                    $action .= '<button type="button" onclick="updateStatus(\'' . $row->id . '\', \'completed\')" class="btn btn-success btn-outline btn-sm">
                                    <i class="fa-solid fa-check"></i>
                                </button>';
                    $action .= '<button type="button" onclick="updateStatus(\'' . $row->id . '\', \'cancelled\')" class="btn btn-error btn-outline btn-sm">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>';
                    // $action .= '<button type="button" onclick="delete_purchase_order(\'' . $row->id . '\')" class="btn btn-success btn-outline btn-sm">
                    //                 <i class="fa-solid fa-check"></i>
                    //             </button>';
                }

                $action .= '<button type="button" onclick="viewPurchaseOrder(\'' . $row->id . '\', \'' . $row->po_number . '\', \'' . $row->vendor . '\', \'' . $row->total_amount . '\', \'' . ucwords($row->status) . '\')" class="btn btn-warning btn-outline btn-sm">
                                <i class="fa-regular fa-eye"></i>
                            </button>';

            $action .= '</div>';

            $data[] = [
                'po_no' => $row->po_number,
                'name' => $row->vendor,
                'total_amount' => number_format($row->total_amount, 2),
                'status' =>  $status,
                'action' => $action
            ];
        }
        return [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ];
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
