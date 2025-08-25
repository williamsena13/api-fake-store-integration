<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;

class CategoryRepository extends AbstractRepository
{
    protected $model = Category::class;

    public function filter($params): Builder
    {
        $query = $this->model->query();

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        return $query;
    }

    public function findByName(string $name): ?Category
    {
        return $this->model->where('name', $name)->first();
    }

    public function getAllWithProductsCount()
    {
        return $this->model->newQuery()
            ->withCount('products')
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'products_count' => $category->products_count,
                    'created_at' => $category->created_at,
                    'updated_at' => $category->updated_at,
                ];
            });
    }
}