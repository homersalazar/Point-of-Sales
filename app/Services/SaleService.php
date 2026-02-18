<?php

namespace App\Services;

use App\Repositories\SaleRepository;

class SaleService extends BaseService
{
    protected $saleRepo;

    public function __construct(SaleRepository $saleRepo)
    {
        parent::__construct($saleRepo);
    }
}
