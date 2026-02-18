<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService extends BaseService
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        parent::__construct($productRepo);
    }

    public function store(array $data)
    {
        $data['sku'] = 'SKU' . now()->format('YmdHis');
        return parent::store($data);
    }
}
