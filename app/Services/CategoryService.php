<?php

namespace App\Services;

use App\Repositories\CategoryRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryService
{
    protected $categoryRepo;

    public function __construct(CategoryRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function search($search = null, $perPage = 10)
    {
        return $this->categoryRepo->paginate($search, $perPage);
    }

    public function getAll()
    {
        return $this->categoryRepo->all();
    }

    public function show($id)
    {
        return $this->categoryRepo->find($id);
    }

    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            $category = $this->categoryRepo->create($data);

            DB::commit();

            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('create category error', ['exception' => $e]);
        }
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();

        try {
            $category = $this->categoryRepo->update($data, $id);

            DB::commit();

            return $category;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('update category error', ['exception' => $e]);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $this->categoryRepo->delete($id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('delete category error', ['exception' => $e]);
        }
    }
}
