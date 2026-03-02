<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;

class ExpenseService extends BaseService
{
    protected ExpenseRepository $expenseRepo;

    public function __construct(ExpenseRepository $expenseRepo)
    {
        parent::__construct($expenseRepo);
        $this->expenseRepo = $expenseRepo;
    }

    public function paginate($search = null, $perPage = 10)
    {
        return $this->expenseRepo->paginate($search, $perPage);
    }

    public function updateStatus($id, $status): array
    {
        $updated = $this->update([
            'status' => $status
        ], $id);

        return $updated
            ? ['success' => true, 'message' => 'Expense status updated successfully.']
            : ['success' => false, 'message' => 'Failed to update expense.'];
    }

    public function totalExpenses($startDate = null, $endDate = null)
    {
        return $this->expenseRepo->totalExpenses($startDate, $endDate);
    }

    public function totalExpensesLastMonth()
    {
        return $this->expenseRepo->totalExpensesLastMonth();
    }

    public function monthlyExpenses()
    {
        return $this->expenseRepo->monthlyExpenses();
    }
}
