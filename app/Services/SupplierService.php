<?php

namespace App\Services;

use App\Repositories\SupplierRepository;

class SupplierService extends BaseService
{
    protected $supplierRepo;

    public function __construct(SupplierRepository $supplierRepo)
    {
        parent::__construct($supplierRepo);
    }
}
