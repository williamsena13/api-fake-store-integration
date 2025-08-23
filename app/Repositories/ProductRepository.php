<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository extends AbstractRepository
{
    protected $model = Product::class;

    public function filter($params): Builder
    {
        $query = $this->model->query()->with('category');

        if (isset($params['category'])) {
            $query->whereHas('category', function ($q) use ($params) {
                $q->where('name', 'like', '%' . $params['category'] . '%');
            });
        }

        if (isset($params['min_price'])) {
            $query->where('price', '>=', $params['min_price']);
        }

        if (isset($params['max_price'])) {
            $query->where('price', '<=', $params['max_price']);
        }

        if (isset($params['q'])) {
            $query->where('title', 'like', '%' . $params['q'] . '%');
        }

        if (isset($params['sort']) && $params['sort'] === 'price') {
            $order = $params['order'] ?? 'asc';
            $query->orderBy('price', $order);
        }

        return $query;
    }

    public function getWithCategory(int $id): ?Product
    {
        return $this->model->with('category')->find($id);
    }
}