<?php

namespace App\Repositories;

use App\Models\Sales;
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
                s.id         AS order_no,
                s.payment_status,
                s.total_amount,
                s.created_at,
                s.sales_status,
                s.payment_method
            FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.id
            ORDER BY s.created_at DESC
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
}
