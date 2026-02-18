<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService extends BaseService
{
    protected $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        parent::__construct($categoryRepo);
    }
}
