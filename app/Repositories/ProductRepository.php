<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function getProductData(Request $request)
    {
        $columns = ['p.name', 'p.sku', 'p.selling_price', 'stock', 'action'];

        $totalData = DB::table('products')->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $query = DB::table('products as p')
            ->select(
                'p.*',
                DB::raw('COALESCE(po.po_qty, 0) - COALESCE(sales.sales_qty, 0) as stock')
            )
            ->leftJoinSub(
                DB::table('purchase_items as pi')
                    ->select('pi.product_id', DB::raw('SUM(pi.quantity) as po_qty'))
                    ->join('purchase_orders as po', 'pi.purchase_order_id', '=', 'po.id')
                    ->where('po.status', 'completed')
                    ->groupBy('pi.product_id'),
                'po',
                'po.product_id',
                '=',
                'p.id'
            )
            ->leftJoinSub(
                DB::table('sale_items as si')
                    ->select('si.product_id', DB::raw('SUM(si.quantity) as sales_qty'))
                    ->join('sales as s', 'si.sale_id', '=', 's.id')
                    ->where('s.sales_status', 'completed')
                    ->groupBy('si.product_id'),
                'sales',
                'sales.product_id',
                '=',
                'p.id'
            );

        if (!empty($search = $request->input('search.value'))) {
            $query->where('p.name', 'LIKE', "%{$search}%")
                ->orWhere('p.selling_price', 'LIKE', "%{$search}%")
                ->orWhere('p.sku', 'LIKE', "%{$search}%");
        }

        $totalFiltered = (clone $query)->count();

        $users = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];
        $color = 'bg-gray-100 text-gray-700';

        foreach ($users as $row) {

            if ($row->stock == 0) {
                $color = 'bg-red-100 text-red-700';
            } elseif ($row->stock <= 5) {
                $color = 'bg-yellow-100 text-yellow-700';
            } elseif ($row->stock >= 10) {
                $color = 'bg-green-100 text-green-700';
            }

            $name = '<div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="mask mask-squircle h-12 w-12">
                                <img
                                    src="' . asset('storage/product/' . $row->image) . '"
                                    alt="' . $row->name . '"
                                />
                            </div>
                        </div>
                        <div>
                            <div class="font-bold">' . $row->name . '</div>
                        </div>
                    </div>';

            $stock = '<span class="px-2 py-1 text-xs font-semibold rounded-full '. $color .'">
                        ' . $row->stock . '
                    </span>';

            $action = '<div class="flex gap-2">
                <button type="button"
                    onclick="update_product(\'' . $row->id . '\', \'' . $row->category_id . '\', \'' . $row->name . '\', \'' . $row->selling_price . '\', \'' . $row->image . '\')"
                    class="btn btn-info btn-outline btn-sm"
                >
                    <i class="fa fa-edit"></i>
                </button>

                <button type="button"
                    onclick="delete_product(\'' . $row->id . '\')"
                    class="btn btn-error btn-outline btn-sm"
                >
                    <i class="fa fa-trash-can"></i>
                </button>
            </div>';

            $data[] = [
                'id' => $row->id,
                'name' => $name,
                'sku' => $row->sku,
                'selling_price' => $row->selling_price,
                'stock' => $stock,
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

    public function countByCategory()
    {
        return DB::select("
            SELECT
                0 AS id,
                'All Product' AS name,
                CONCAT(COUNT(*), ' items') AS total_products
            FROM products

            UNION ALL

            SELECT
                c.id,
                c.name,
                CONCAT(COUNT(p.id), ' items') AS total_products
            FROM categories c
            LEFT JOIN products p
                ON p.category_id = c.id
            GROUP BY c.id, c.name

            ORDER BY id ASC
        ");
    }

    public function countByStockStatus()
    {
        return DB::table('products as p')
            ->select(
                'p.name',
                'p.selling_price',
                DB::raw('COALESCE(po.po_qty, 0) - COALESCE(sales.sales_qty, 0) as stock')
            )
            ->leftJoinSub(
                DB::table('purchase_items as pi')
                    ->select('pi.product_id', DB::raw('SUM(pi.quantity) as po_qty'))
                    ->join('purchase_orders as po', 'pi.purchase_order_id', '=', 'po.id')
                    ->where('po.status', 'completed')
                    ->groupBy('pi.product_id'),
                'po',
                'po.product_id',
                '=',
                'p.id'
            )
            ->leftJoinSub(
                DB::table('sale_items as si')
                    ->select('si.product_id', DB::raw('SUM(si.quantity) as sales_qty'))
                    ->join('sales as s', 'si.sale_id', '=', 's.id')
                    ->where('s.sales_status', 'completed')
                    ->groupBy('si.product_id'),
                'sales',
                'sales.product_id',
                '=',
                'p.id'
            )
            ->paginate(10);
    }

    public function searchByName($name)
    {
        return $this->model
            ->where('name', 'like', "%{$name}%")
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();
    }

    public function countAll()
    {
        return $this->model->count();
    }
}
