<?php

namespace App\Integrator\FakeStore\Strategies;

use Illuminate\Support\Facades\Cache;

class DefaultCacheInvalidationStrategy implements CacheInvalidationStrategyInterface
{
    public function invalidate(): void
    {
        Cache::forget('products.list');
        Cache::forget('products.stats');
        Cache::tags(['products', 'categories'])->flush();
    }
}