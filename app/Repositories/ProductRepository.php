<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function paginate($search = null, $perPage = 10)
    {
        return Product::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('cost_price', 'like', "%{$search}%")
                ->orWhere('selling_price', 'like', "%{$search}%")
                ->orWhere('stock', 'like', "%{$search}%");
        })
            ->orderBy('name', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $search]);
    }

    public function all()
    {
        return Product::latest()->get();
    }

    public function create(array $data)
    {
        return Product::create($data);
    }

    public function find($id)
    {
        return Product::findOrFail($id);
    }

    public function update(array $data, $id)
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = $this->find($id);
        $product->delete();
    }
}
