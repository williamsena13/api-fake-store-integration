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

        return $query;
    }

    public function index($params)
    {
        $filter = $this->filter($params);

        if (isset($params['sort'])) {
            $order = $params['order'] ?? 'asc';

            switch ($params['sort']) {
                case 'title':
                    $filter->orderBy('title', $order);
                    break;
                case 'price':
                    $filter->orderBy('price', $order);
                    break;
                case 'category.name':
                    $filter->join('categories', 'products.category_id', '=', 'categories.id')
                           ->orderBy('categories.name', $order)
                           ->select('products.*');
                    break;
                case 'created_at':
                    $filter->orderBy('created_at', $order);
                    break;
                case 'updated_at':
                    $filter->orderBy('updated_at', $order);
                    break;
                default:
                    $filter->orderBy('id', 'desc');
            }
        } else {
            $filter->orderBy('id', 'desc');
        }

        $page = $params['page'] ?? 1;
        $per_page = $params['per_page'] ?? 15;

        return $filter->paginate($per_page, ['*'], 'page', $page);
    }

    public function getWithCategory(int $id): ?Product
    {
        return $this->model->with('category')->find($id);
    }
}
