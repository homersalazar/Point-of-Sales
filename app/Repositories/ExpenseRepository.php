<?php

namespace App\Repositories;

use App\Models\Expense;
use Carbon\Carbon;

class ExpenseRepository extends BaseRepository
{
    public function __construct(Expense $model)
    {
        parent::__construct($model);
    }

    public function paginate($search = null, $perPage = 10)
    {
        return $this->model
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('expense_categories.name', 'like', "%{$search}%")
                        ->orWhere('expenses.amount', 'like', "%{$search}%")
                        ->orWhere('expenses.description', 'like', "%{$search}%")
                        ->orWhere('expenses.expense_date', 'like', "%{$search}%");
                });
            })
            ->orderBy('expense_categories.name', 'asc')
            ->select(
                'expenses.*',
                'expense_categories.id as category_id',
                'expense_categories.name as category_name'
            )
            ->paginate($perPage)
            ->appends(['search' => $search]);
    }

    public function totalExpenses($startdate = null, $enddate = null)
    {
        $start = $startdate
            ? Carbon::parse($startdate)->startOfDay()
            : Carbon::now()->startOfMonth();   // ✅ start of current month

        $end = $enddate
            ? Carbon::parse($enddate)->endOfDay()
            : Carbon::now()->endOfMonth();     // ✅ end of current month

        return Expense::whereBetween('expense_date', [$start, $end])
                    ->where('status', 'completed')
                    ->sum('amount');
    }

    public function totalExpensesLastMonth()
    {
        return Expense::whereBetween('expense_date', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->where('status', 'completed')
            ->sum('amount');
    }

    public function monthlyExpenses($year = null)
    {
        $year = $year ?? now()->year;

        return Expense::selectRaw('MONTH(expense_date) as month, SUM(amount) as total')
                    ->whereYear('expense_date', $year)
                    ->where('status', 'completed')
                    ->groupBy('month')
                    ->pluck('total', 'month')
                    ->toArray();
    }
}
