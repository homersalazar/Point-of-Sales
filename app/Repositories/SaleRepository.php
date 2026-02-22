<?php

namespace App\Repositories;

use App\Models\Sales;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleRepository extends BaseRepository
{
    public function __construct(Sales $model)
    {
        parent::__construct($model);
    }

    public function findAllSales()
    {
        return DB::select("
            SELECT
                c.name,
                s.id AS order_no,
                s.payment_status,
                s.total_amount,
                s.created_at,
                s.sales_status,
                s.payment_method
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            WHERE s.created_at >= CURDATE()
            AND s.created_at < CURDATE() + INTERVAL 1 DAY
            ORDER BY
                CASE s.sales_status
                    WHEN 'pending' THEN 0
                    ELSE 1
                END,
                s.created_at DESC;
        ");
    }

    public function findSaleItems(int $id)
    {
        return DB::select("
            SELECT
                p.name      AS item,
                si.quantity AS qty,
                si.price,
                si.subtotal
            FROM sales s
            LEFT JOIN sale_items si ON s.id = si.sale_id
            LEFT JOIN products p   ON p.id = si.product_id
            WHERE s.id = ?
        ", [$id]);
    }  // ✅ properly closed

    public function countByOrderStatus()
    {
        $counts = Sales::selectRaw("
            COUNT(*) as all_products,
            SUM(sales_status = 'pending') as pending,
            SUM(sales_status = 'cancelled') as cancelled,
            SUM(sales_status = 'completed') as completed
        ")
            ->whereBetween('created_at', [
                Carbon::today()->startOfDay(), // today 00:00:00
                Carbon::today()->endOfDay()    // today 23:59:59
            ])
            ->first();

        return collect([
            (object)[
                'id' => 0,
                'name' => 'All Product',
                'total_products' => $counts->all_products . ' items',
            ],
            (object)[
                'id' => 1,
                'name' => 'Pending',
                'total_products' => $counts->pending . ' items',
            ],
            (object)[
                'id' => 2,
                'name' => 'Cancelled',
                'total_products' => $counts->cancelled . ' items',
            ],
            (object)[
                'id' => 3,
                'name' => 'Completed',
                'total_products' => $counts->completed . ' items',
            ],
        ]);
    }
}
