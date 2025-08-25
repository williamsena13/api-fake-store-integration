<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Services\ProductService;
use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $products = $this->productService->getProducts($request->all());
            return response()->json($products);
        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            return response()->json($product);
        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function activity(int $id): JsonResponse
    {
        try {
            $activities = $this->productService->getProductActivity($id);
            return response()->json($activities);
        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function deleteAll(): JsonResponse
    {
        try {
            $deletedCount = $this->productService->deleteAllProducts();
            return response()->json([
                'success' => true,
                'message' => 'Todos os produtos foram excluídos com sucesso',
                'deleted_count' => $deletedCount
            ]);
        } catch (BusinessException $e) {
            return $this->errorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}