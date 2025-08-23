<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Exceptions\BusinessException;
use Illuminate\Http\JsonResponse;

class StatsController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $stats = $this->productService->getStats();
            return response()->json($stats);
        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
