<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductService
{
    protected $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    public function search($search = null, $perPage = 10)
    {
        return $this->productRepo->paginate($search, $perPage);
    }

    public function getAll()
    {
        return $this->productRepo->all();
    }

    public function show($id)
    {
        return $this->productRepo->find($id);
    }

    public function store(array $data)
    {
        DB::beginTransaction();

        try {
            $product = $this->productRepo->create($data);

            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('create product error', ['exception' => $e]);
        }
    }

    public function update(array $data, $id)
    {
        DB::beginTransaction();

        try {
            $product = $this->productRepo->update($data, $id);

            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('update product error', ['exception' => $e]);
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $this->productRepo->delete($id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('delete product error', ['exception' => $e]);
        }
    }
}
