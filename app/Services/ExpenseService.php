<?php

namespace App\Services;

use App\Repositories\ExpenseRepository;

class ExpenseService extends BaseService
{
    protected $expenseRepo;

    public function __construct(ExpenseRepository $expenseRepo)
    {
        parent::__construct($expenseRepo);
    }
}
