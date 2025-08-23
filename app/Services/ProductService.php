<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

class ProductService extends AbstractService
{
    public function __construct(ProductRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function getAllowedFilters(): array
    {
        return array_merge(parent::getAllowedFilters(), [
            'category', 'min_price', 'max_price', 'q', 'sort'
        ]);
    }

    public function getProducts(array $params)
    {
        $params = $this->validatePagination($params);
        $filters = $this->sanitizeFilters($params);

        return $this->repository->index($filters);
    }

    public function getProductById(int $id)
    {
        $product = $this->repository->getWithCategory($id);

        if (empty($product)) {
            $this->findOrFailBusiness($id, 'Product not found');
        }

        return $product;
    }

    public function getStats()
    {
        $totalProducts = DB::table('products')->whereNull('deleted_at')->count();
        $avgPrice = DB::table('products')->whereNull('deleted_at')->avg('price');

        $byCategory = DB::select("
            SELECT c.name as category
                 , COUNT(p.id) as total
              FROM categories c
              JOIN products p ON c.id = p.category_id
             WHERE p.deleted_at IS NULL AND c.deleted_at IS NULL
             GROUP BY c.id, c.name
        ");

        $top5Expensive = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category_name')
            ->whereNull('products.deleted_at')
            ->whereNull('categories.deleted_at')
            ->orderBy('products.price', 'desc')
            ->limit(5)
            ->get();

        return [
            'total_products' => $totalProducts,
            'avg_price' => round($avgPrice, 2),
            'by_category' => $byCategory,
            'top5_expensive' => $top5Expensive
        ];
    }

    public function deleteAllProducts(): int
    {
        return $this->withTransaction(function () {
            $model = $this->repository->getModel();
            $count = $model::withTrashed()->count();
            $model::withTrashed()->forceDelete();
            return $count;
        });
    }
}
