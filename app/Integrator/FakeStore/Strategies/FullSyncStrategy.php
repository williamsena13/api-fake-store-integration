<?php

namespace App\Integrator\FakeStore\Strategies;

use App\Integrator\FakeStore\DTOs\ProductDTO;
use App\Integrator\FakeStore\DTOs\SyncResultDTO;
use App\Integrator\FakeStore\FakeStoreClient;
use App\Logging\IntegrationLogger;
use App\Services\CategoryService;
use App\Repositories\ProductRepository;

class FullSyncStrategy implements SyncStrategyInterface
{
    public function __construct(
        private FakeStoreClient $client,
        private CategoryService $categoryService,
        private ProductRepository $productRepository
    ) {}

    public function sync(): SyncResultDTO
    {
        $result = new SyncResultDTO();

        try {
            $products = $this->client->getAllProducts();

            foreach ($products as $productData) {
                try {
                    $productDTO = ProductDTO::fromArray($productData);
                    $this->syncProduct($productDTO, $result);
                } catch (\Exception $e) {
                    $result->addError(
                        "Failed to sync product: {$e->getMessage()}",
                        ['product_id' => $productData['id'] ?? 'unknown']
                    );
                    IntegrationLogger::error('Product sync error', [
                        'product' => $productData,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            $result->addError("Failed to fetch products: {$e->getMessage()}");
            IntegrationLogger::error('Full sync error', ['error' => $e->getMessage()]);
        }

        return $result;
    }

    private function syncProduct(ProductDTO $productDTO, SyncResultDTO $result): void
    {
        $category = $this->categoryService->findOrCreateByName($productDTO->category);
        
        $productData = $productDTO->toArray();
        $productData['category_id'] = $category->id;

        $existingProduct = $this->productRepository->getByExternalId($productDTO->id);

        if ($existingProduct) {
            $this->productRepository->updateById($productData, $existingProduct->id);
            $result->addUpdated();
        } else {
            $this->productRepository->store($productData);
            $result->addImported();
        }
    }
}