<?php

namespace App\Services;

use App\Repositories\CustomerRepository;

class CustomerService extends BaseService
{
    protected $customerRepo;

    public function __construct(CustomerRepository $customerRepo)
    {
        parent::__construct($customerRepo);
    }
}
