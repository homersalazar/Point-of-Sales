<?php

namespace App\Repositories;

use App\Models\Expense_category;

class ExpenseCategoryRepository extends BaseRepository
{
    public function __construct(Expense_category $model)
    {
        parent::__construct($model);
    }

    public function paginate($search = null, $perPage = 10)
    {
        return $this->model->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })
            ->orderBy('name', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $search]);
    }
}
