<?php

namespace App\Integrator\FakeStore;

use App\Integrator\FakeStore\Strategies\SyncStrategyInterface;
use App\Integrator\FakeStore\Strategies\FullSyncStrategy;
use App\Integrator\FakeStore\Strategies\DeltaSyncStrategy;
use App\Integrator\FakeStore\Strategies\LimitedSyncStrategy;
use App\Services\CategoryService;
use App\Repositories\ProductRepository;

class SyncContext
{
    public function __construct(
        private FakeStoreClient $client,
        private CategoryService $categoryService,
        private ProductRepository $productRepository
    ) {}

    public function getStrategy(string $mode, ?int $limit = null): SyncStrategyInterface
    {
        return match ($mode) {
            'delta' => new DeltaSyncStrategy(
                $this->client,
                $this->categoryService,
                $this->productRepository
            ),
            'limited' => new LimitedSyncStrategy(
                $this->client,
                $this->categoryService,
                $this->productRepository,
                $limit ?? 10
            ),
            default => new FullSyncStrategy(
                $this->client,
                $this->categoryService,
                $this->productRepository
            )
        };
    }
}