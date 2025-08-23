<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(): JsonResponse
    {
        $stats = $this->productService->getStats();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
