<?php

namespace App\Services;

use App\Repositories\ProductRepository;

class ProductService extends BaseService
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        parent::__construct($productRepo);
        $this->productRepo = $productRepo;
    }

    public function store(array $data)
    {
        $data['sku'] = 'SKU' . now()->format('YmdHis');
        return parent::store($data);
    }

    public function countByCategory()
    {
        return $this->productRepo->countByCategory();
    }

    public function searchByName($name)
    {
        return $this->productRepo->searchByName($name);
    }

    public function countByStockStatus()
    {
        return $this->productRepo->countByStockStatus();
    }
}
