<?php

namespace App\Http\Controllers;

use App\Services\ExpenseService;
use App\Services\ProductService;
use App\Services\PurchaseOrderService;
use App\Services\SaleService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $saleService;
    protected $expenseService;
    protected $purchaseOrderService;
    protected $productService;

    public function __construct(
        SaleService $saleService,
        ExpenseService $expenseService,
        PurchaseOrderService $purchaseOrderService,
        ProductService $productService

    ) {
        $this->saleService = $saleService;
        $this->expenseService = $expenseService;
        $this->purchaseOrderService = $purchaseOrderService;
        $this->productService = $productService;
    }

    private function formatNumberShort($number)
    {
        if ($number >= 1000000) {
            return rtrim(rtrim(number_format($number / 1000000, 1), '0'), '.') . 'M';
        }

        if ($number >= 1000) {
            return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.') . 'k';
        }

        return number_format($number, 2);
    }

    public function index(Request $request)
    {
        // ===== RAW TOTALS =====
        $raw_sales = $this->saleService->totalSales();
        $raw_expenses = $this->expenseService->totalExpenses();
        $raw_purchases = $this->purchaseOrderService->totalPurchaseOrders();

        // ===== REVENUE =====
        $raw_revenue = $raw_sales - ($raw_expenses + $raw_purchases);

        // ===== MONTH COMPARISON =====
        $lastMonthSales = $this->saleService->totalSalesLastMonth();
        $lastMonthExpenses = $this->expenseService->totalExpensesLastMonth();
        $lastMonthPurchases = $this->purchaseOrderService->totalPurchaseOrdersLastMonth();
        $lastMonthRevenue = $lastMonthSales - ($lastMonthExpenses + $lastMonthPurchases);

        if ($lastMonthSales > 0 && $lastMonthExpenses > 0 && $lastMonthPurchases > 0 && $lastMonthRevenue > 0) {
            $salesPercentageChange = $lastMonthSales > 0
                ? (($raw_sales - $lastMonthSales) / $lastMonthSales) * 100
                : 0;

            $purchasesPercentageChange = $lastMonthPurchases > 0
                ? (($raw_purchases - $lastMonthPurchases) / $lastMonthPurchases) * 100
                : 0;

            $revenuePercentageChange = $lastMonthRevenue != 0
                ? (($raw_revenue - $lastMonthRevenue) / abs($lastMonthRevenue)) * 100
                : 0;
        } else {
            $salesPercentageChange = 0;
            $expensesPercentageChange = 0;
            $purchasesPercentageChange = 0;
            $revenuePercentageChange = 0;
        }

        $salesPercentageChange = round($salesPercentageChange, 1);
        $expensesPercentageChange = round($expensesPercentageChange, 1);
        $purchasesPercentageChange = round($purchasesPercentageChange, 1);

        // ===== FORMAT FOR DISPLAY =====
        $total_sales = $this->formatNumberShort($raw_sales);
        $total_expenses = $this->formatNumberShort($raw_expenses);
        $total_purchases = $this->formatNumberShort($raw_purchases);
        $total_revenue = $this->formatNumberShort($raw_revenue);

        $monthlySales = $this->saleService->monthlySales();
        $monthlyPurchases = $this->purchaseOrderService->monthlyPurchases();
        $monthlyExpenses = $this->expenseService->monthlyExpenses();

        $monthlyTotalPurchases = [];
        $monthlyTotalSales = [];

        for ($i = 1; $i <= 12; $i++) {
            $monthlyTotalPurchases[$i-1] =
                ($monthlyPurchases[$i] ?? 0) + ($monthlyExpenses[$i] ?? 0);
                $monthlyTotalSales[$i-1] = (float)($monthlySales[$i] ?? 0);
        }

        // ===== STOCK ALERT FOR DISPLAY =====
        $lowStockProducts = $this->productService->countByStockStatus();

        return view('dashboard.index', compact(
            'total_sales',
            'total_purchases',
            'total_expenses',
            'total_revenue',
            'salesPercentageChange',
            'expensesPercentageChange',
            'purchasesPercentageChange',
            'revenuePercentageChange',
            'monthlyTotalSales',
            'monthlyTotalPurchases',
            'lowStockProducts'
        ));
    }
}
