<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

abstract class AbstractRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    protected function resolveModel()
    {
        return app($this->model);
    }

    public function index($params)
    {
        $filter = $this->filter($params);
        return $this->paginate($filter, $params);
    }

    public function paginate(Builder $filter, $params)
    {
        $orderBy = $params['order_by'] ?? '';
        $order = $params['order'] ?? 'asc';
        $page = $params['page'] ?? 1;
        $per_page = $params['per_page'] ?? 15;

        if (empty($filter)) {
            $filter = $this->model;
        }

        if (strlen($orderBy) > 0) {
            $filter->orderBy($orderBy, $order);
        }

        return $filter->paginate($per_page, ['*'], 'page', $page);
    }

    public function filter($params): Builder
    {
        return $this->model->query();
    }

    public function all()
    {
        return $this->model->all();
    }

    public function show(int $id)
    {
        return $this->getByWhere('id', $id);
    }

    public function getById($id)
    {
        return $this->model->find($id);
    }

    public function getByExternalId($externalId)
    {
        return $this->model->where('external_id', $externalId)->first();
    }

    public function getByWhere($column, $condition, $first = true)
    {
        $query = $this->model->where($column, $condition);
        if (empty($first)) {
            return $query->get();
        }

        return $query->first();
    }

    public function store(array $request)
    {
        return $this->model->create($request);
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        return $this->model->updateOrCreate($attributes, $values);
    }

    public function updateById(array $fields, int $id)
    {
        $model = $this->model->findOrFail($id);
        $model->fill($fields);
        $model->save();
        return $model;
    }

    public function deleteById(int $id)
    {
        $entity = $this->model->find($id);
        return $entity->delete();
    }

    public function getModel()
    {
        return $this->model;
    }
}
