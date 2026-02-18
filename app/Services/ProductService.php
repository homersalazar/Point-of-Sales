<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService extends BaseService
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        parent::__construct($productRepo);
    }
}
