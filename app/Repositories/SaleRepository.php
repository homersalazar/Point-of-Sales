<?php

namespace App\Repositories;

use App\Models\Sales;

class SaleRepository extends BaseRepository
{
    public function __construct(Sales $model)
    {
        parent::__construct($model);
    }
}
