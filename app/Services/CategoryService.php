<?php

namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService extends AbstractService
{
    public function __construct(CategoryRepository $repository)
    {
        parent::__construct($repository);
    }

    protected function getAllowedFilters(): array
    {
        return array_merge(parent::getAllowedFilters(), ['name']);
    }

    public function findOrCreateByName(string $name)
    {
        $category = $this->repository->findByName($name);

        if (empty($category)) {
            $category = $this->repository->store(['name' => $name]);
        }

        return $category;
    }

    public function getAllWithProductsCount()
    {
        return $this->repository->getAllWithProductsCount();
    }
}
