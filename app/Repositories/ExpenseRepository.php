<?php

namespace App\Repositories;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseRepository extends BaseRepository
{
    public function __construct(Expense $model)
    {
        parent::__construct($model);
    }

    public function getExpensesData(Request $request)
    {
        $columns = ['expense_categories.name', 'expenses.amount', 'expenses.expense_date', 'expenses.description', 'expenses.status', 'action'];

        $totalData = DB::table('expenses')->count();

        $limit = $request->input('length');
        $start = $request->input('start');
        $orderIndex = $request->input('order.0.column', 0);
        $order = $columns[$orderIndex] ?? 'expenses.expense_date';
        $dir = $request->input('order.0.dir', 'desc');

        $query = DB::table('expenses')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->select('expenses.id', 'expenses.expense_category_id AS category_id', 'expenses.amount', 'expenses.expense_date', 'expenses.description', 'expenses.status', DB::raw('COALESCE(expense_categories.name, "") as category_name'));

        if (!empty($search = $request->input('search.value'))) {
            $query->where(function ($q) use ($search) {
                $q->where('expenses.expense_date', 'LIKE', "%{$search}%")
                ->orWhere('expenses.amount', 'LIKE', "%{$search}%")
                ->orWhere('expenses.status', 'LIKE', "%{$search}%")
                ->orWhere('expenses.description', 'LIKE', "%{$search}%")
                ->orWhere('expense_categories.name', 'LIKE', "%{$search}%");
            });
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

                    $action .= '<button type="button"
                        onclick="update_expense(
                            \'' . $row->id . '\',
                            \'' . $row->category_id . '\',
                            \'' . $row->amount . '\',
                            \'' . addslashes($row->description) . '\',
                            \'' . $row->expense_date . '\'
                        )"
                        class="btn btn-info btn-outline btn-sm">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>';

                    $action .= '<button type="button" onclick="delete_expense(\'' . $row->id . '\')" class="btn btn-error btn-outline btn-sm">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>';
                }

            $action .= '</div>';

            $data[] = [
                'name' => $row->category_name,
                'amount' => number_format($row->amount, 2),
                'expense_date' => date('F j, Y', strtotime($row->expense_date)),
                'description' => $row->description,
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
