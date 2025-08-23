<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Exceptions\BusinessException;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(): JsonResponse
    {
        try {
            $categories = $this->categoryService->getAllWithProductsCount();
            return response()->json($categories);
        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
