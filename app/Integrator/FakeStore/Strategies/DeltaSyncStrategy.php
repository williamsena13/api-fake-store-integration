<?php

namespace App\Integrator\FakeStore\Strategies;

use App\Integrator\FakeStore\DTOs\ProductDTO;
use App\Integrator\FakeStore\DTOs\SyncResultDTO;
use App\Integrator\FakeStore\FakeStoreClient;
use App\Logging\IntegrationLogger;
use App\Services\CategoryService;
use App\Repositories\ProductRepository;
use Carbon\Carbon;

class DeltaSyncStrategy implements SyncStrategyInterface
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
            $products = $this->client->getProducts(5);
            $lastSync = Carbon::now()->subHour();

            foreach ($products as $productData) {
                try {
                    $productDTO = ProductDTO::fromArray($productData);
                    $existingProduct = $this->productRepository->getByExternalId($productDTO->id);

                    if ($existingProduct && $existingProduct->updated_at->gt($lastSync)) {
                        $result->addSkipped();
                        continue;
                    }

                    $this->syncProduct($productDTO, $result, $existingProduct);
                } catch (\Exception $e) {
                    $result->addError(
                        "Failed to sync product: {$e->getMessage()}",
                        ['product_id' => $productData['id'] ?? 'unknown']
                    );
                    IntegrationLogger::error('Product delta sync error', [
                        'product' => $productData,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (\Exception $e) {
            $result->addError("Failed to fetch products: {$e->getMessage()}");
            IntegrationLogger::error('Delta sync error', ['error' => $e->getMessage()]);
        }

        return $result;
    }

    private function syncProduct(ProductDTO $productDTO, SyncResultDTO $result, $existingProduct): void
    {
        $category = $this->categoryService->findOrCreateByName($productDTO->category);

        $productData = $productDTO->toArray();
        $productData['category_id'] = $category->id;

        if ($existingProduct) {
            $this->productRepository->updateById($productData, $existingProduct->id);
            $result->addUpdated();
        } else {
            $this->productRepository->store($productData);
            $result->addImported();
        }
    }
}
