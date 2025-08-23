<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->getProducts($request->all());

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getProductById($id);

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    public function activity(int $id): JsonResponse
    {
        $activities = $this->productService->getProductActivity($id);

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function deleteAll(): JsonResponse
    {
        $deletedCount = $this->productService->deleteAllProducts();

        return response()->json([
            'success' => true,
            'message' => 'Todos os produtos foram excluídos com sucesso',
            'deleted_count' => $deletedCount
        ]);
    }
}