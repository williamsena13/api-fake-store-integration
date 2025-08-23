<?php

namespace App\Integrator\FakeStore\Strategies;

use App\Integrator\FakeStore\DTOs\SyncResultDTO;

interface SyncStrategyInterface
{
    public function sync(): SyncResultDTO;
}