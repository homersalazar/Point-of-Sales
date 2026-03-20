<?php

namespace App\Repositories;

use App\Models\Expense_category;

class ExpenseCategoryRepository extends BaseRepository
{
    public function __construct(Expense_category $model)
    {
        parent::__construct($model);
    }
}
