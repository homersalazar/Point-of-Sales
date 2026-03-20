<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());

        $start = $startDate . ' 00:00:00';
        $end   = $endDate   . ' 23:59:59';

        // ── KPI COUNTS ──────────────────────────────────────────────
        $kpi = DB::selectOne("
            SELECT
                COUNT(*)                                                      AS total_orders,
                SUM(CASE WHEN sales_status = 'completed' THEN total_amount ELSE 0 END) AS total_revenue,
                SUM(CASE WHEN sales_status = 'completed' THEN 1 ELSE 0 END)  AS completed_count,
                SUM(CASE WHEN sales_status = 'cancelled' THEN 1 ELSE 0 END)  AS cancelled_count,
                SUM(CASE WHEN sales_status = 'pending'   THEN 1 ELSE 0 END)  AS pending_count
            FROM sales
            WHERE created_at BETWEEN ? AND ?
        ", [$start, $end]);

        // ── PAGINATED TRANSACTION TABLE ──────────────────────────────
        $perPage     = 20;
        $currentPage = (int) $request->get('page', 1);
        $offset      = ($currentPage - 1) * $perPage;

        $totalRows = DB::selectOne("
            SELECT COUNT(*) AS cnt FROM sales WHERE created_at BETWEEN ? AND ?
        ", [$start, $end])->cnt;

        $sales = DB::select("
            SELECT
                s.id,
                s.invoice_no,
                s.total_amount,
                s.payment_method,
                s.payment_status,
                s.sales_status,
                s.created_at,
                COALESCE(c.name, 'Walk-in') AS customer_name,
                COALESCE(u.name, '—')       AS cashier_name
            FROM sales s
            LEFT JOIN customers c ON c.id = s.customer_id
            LEFT JOIN users     u ON u.id = s.user_id
            WHERE s.created_at BETWEEN ? AND ?
            ORDER BY s.created_at DESC
            LIMIT ? OFFSET ?
        ", [$start, $end, $perPage, $offset]);

        // Attach sale items to each row
        if (!empty($sales)) {
            $saleIds      = array_column($sales, 'id');
            $placeholders = implode(',', array_fill(0, count($saleIds), '?'));

            $saleItems = DB::select("
                SELECT si.sale_id, si.quantity, p.name AS product_name
                FROM sale_items si
                JOIN products p ON p.id = si.product_id
                WHERE si.sale_id IN ($placeholders)
            ", $saleIds);

            $itemsBySale = [];
            foreach ($saleItems as $item) {
                $itemsBySale[$item->sale_id][] = $item;
            }
            foreach ($sales as $sale) {
                $sale->items = $itemsBySale[$sale->id] ?? [];
            }
        }

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $sales, $totalRows, $perPage, $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // ── TOP SELLING PRODUCTS ─────────────────────────────────────
        $topProducts = DB::select("
            SELECT
                p.name           AS product_name,
                cat.name         AS category_name,
                SUM(si.quantity) AS total_qty,
                SUM(si.subtotal) AS total_revenue
            FROM sale_items si
            JOIN products   p   ON p.id   = si.product_id
            JOIN categories cat ON cat.id = p.category_id
            JOIN sales      s   ON s.id   = si.sale_id
            WHERE s.created_at BETWEEN ? AND ?
            GROUP BY p.id, p.name, cat.name
            ORDER BY total_revenue DESC
            LIMIT 10
        ", [$start, $end]);

        // ── REVENUE BY CATEGORY ──────────────────────────────────────
        $byCategory = DB::select("
            SELECT
                cat.name         AS category_name,
                SUM(si.subtotal) AS revenue
            FROM sale_items si
            JOIN products   p   ON p.id   = si.product_id
            JOIN categories cat ON cat.id = p.category_id
            JOIN sales      s   ON s.id   = si.sale_id
            WHERE s.created_at BETWEEN ? AND ?
            GROUP BY cat.id, cat.name
            ORDER BY revenue DESC
        ", [$start, $end]);

        // ── PAYMENT METHOD BREAKDOWN ─────────────────────────────────
        $byPayment = DB::select("
            SELECT
                payment_method,
                COUNT(*)          AS txn_count,
                SUM(total_amount) AS total
            FROM sales
            WHERE created_at BETWEEN ? AND ?
            GROUP BY payment_method
        ", [$start, $end]);

        return view('reports.sales', compact(
            'kpi', 'sales', 'paginator',
            'topProducts', 'byCategory', 'byPayment',
            'startDate', 'endDate'
        ));
    }

    // ── CSV EXPORT ───────────────────────────────────────────────────
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());
        $start     = $startDate . ' 00:00:00';
        $end       = $endDate   . ' 23:59:59';

        $sales = DB::select("
            SELECT
                s.id,
                s.invoice_no,
                s.created_at,
                COALESCE(c.name, 'Walk-in') AS customer_name,
                COALESCE(u.name, '—')       AS cashier_name,
                s.payment_method,
                s.total_amount,
                s.payment_status,
                s.sales_status
            FROM sales s
            LEFT JOIN customers c ON c.id = s.customer_id
            LEFT JOIN users     u ON u.id = s.user_id
            WHERE s.created_at BETWEEN ? AND ?
            ORDER BY s.created_at DESC
        ", [$start, $end]);

        if (!empty($sales)) {
            $saleIds      = array_column($sales, 'id');
            $placeholders = implode(',', array_fill(0, count($saleIds), '?'));

            $items = DB::select("
                SELECT si.sale_id, si.quantity, p.name AS product_name
                FROM sale_items si
                JOIN products p ON p.id = si.product_id
                WHERE si.sale_id IN ($placeholders)
            ", $saleIds);

            $itemsBySaleId = [];
            foreach ($items as $item) {
                $itemsBySaleId[$item->sale_id][] = $item->quantity . 'x ' . $item->product_name;
            }
            foreach ($sales as $sale) {
                $sale->items_summary = implode(', ', $itemsBySaleId[$sale->id] ?? []);
            }
        }

        $filename = "sales_report_{$startDate}_to_{$endDate}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice No.', 'Date', 'Customer', 'Cashier', 'Items',
                              'Payment Method', 'Total Amount', 'Payment Status', 'Sales Status']);
            foreach ($sales as $sale) {
                fputcsv($handle, [
                    $sale->invoice_no, $sale->created_at,
                    $sale->customer_name, $sale->cashier_name,
                    $sale->items_summary ?? '',
                    $sale->payment_method,
                    number_format($sale->total_amount, 2),
                    $sale->payment_status, $sale->sales_status,
                ]);
            }
            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }
}
