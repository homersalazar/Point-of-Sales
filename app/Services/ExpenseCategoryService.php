<?php

namespace App\Services;

use App\Repositories\ExpenseCategoryRepository;

class ExpenseCategoryService extends BaseService
{
    protected $expenseCategoryRepo;

    public function __construct(ExpenseCategoryRepository $expenseCategoryRepo)
    {
        parent::__construct($expenseCategoryRepo);
    }
}
