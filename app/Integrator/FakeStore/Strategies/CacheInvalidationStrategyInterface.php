<?php

namespace App\Integrator\FakeStore\Strategies;

interface CacheInvalidationStrategyInterface
{
    public function invalidate(): void;
}