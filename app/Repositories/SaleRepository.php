<?php

namespace App\Repositories;

use App\Models\Sales;

class SaleRepository extends BaseRepository
{
    public function __construct(Sales $model)
    {
        parent::__construct($model);
    }

    // public function paginate($search = null, $perPage = 10)
    // {
    //     return $this->model->when($search, function ($query) use ($search) {
    //         $query->where('name', 'like', "%{$search}%")
    //             ->orWhere('cost_price', 'like', "%{$search}%")
    //             ->orWhere('selling_price', 'like', "%{$search}%")
    //             ->orWhere('stock', 'like', "%{$search}%");
    //     })
    //         ->orderBy('name', 'asc')
    //         ->paginate($perPage)
    //         ->appends(['search' => $search]);
    // }
}
