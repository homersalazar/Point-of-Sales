<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function paginate($search = null, $perPage = 10)
    {
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

        // Apply search if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('p.name', 'like', "%{$search}%")
                ->orWhere('p.selling_price', 'like', "%{$search}%");
            });
        }

        // Order and paginate
        return $query->orderBy('p.name', 'asc')
                    ->paginate($perPage)
                    ->appends(['search' => $search]);
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
