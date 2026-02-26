<?php

namespace App\Repositories;

use App\Models\Expense;

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
}
