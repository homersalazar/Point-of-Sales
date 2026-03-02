<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function paginate($search = null, $perPage = 10)
    {
        return $this->model->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('cost_price', 'like', "%{$search}%")
                ->orWhere('selling_price', 'like', "%{$search}%")
                ->orWhere('stock', 'like', "%{$search}%");
        })
            ->orderBy('name', 'asc')
            ->paginate($perPage)
            ->appends(['search' => $search]);
    }

    public function countByCategory()
    {
        return DB::select("
            SELECT
                0 AS id,
                'All Product' AS name,
                CONCAT(COUNT(*), ' items') AS total_products
            FROM products

            UNION ALL

            SELECT
                c.id,
                c.name,
                CONCAT(COUNT(p.id), ' items') AS total_products
            FROM categories c
            LEFT JOIN products p
                ON p.category_id = c.id
            GROUP BY c.id, c.name

            ORDER BY id ASC
        ");
    }

    public function countByStockStatus()
    {
        return DB::table('products')
            ->select('name', 'stock')
            ->where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->paginate(10);
    }

    public function searchByName($name)
    {
        return $this->model
            ->where('name', 'like', "%{$name}%")
            ->orderBy('name', 'asc')
            ->limit(10)
            ->get();
    }
}
