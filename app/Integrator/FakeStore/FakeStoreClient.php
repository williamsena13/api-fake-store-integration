<?php

namespace App\Integrator\FakeStore;

use App\Exceptions\BusinessException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class FakeStoreClient
{
    private string $baseUrl;
    private int $timeout;
    private int $retries;
    private int $limitProducts = 100;// FakeStore API tem limite máximo, vamos buscar mais produtos

    public function __construct()
    {
        $this->baseUrl = env('FAKESTORE_BASE_URL', 'https://fakestoreapi.com');
        $this->timeout = env('FAKESTORE_TIMEOUT', 30);
        $this->retries = env('FAKESTORE_RETRIES', 3);
    }

    public function getProducts(int $limit = null): array
    {
        $endpoint = '/products';
        if (isset($limit)) {
            $endpoint .= "?limit={$limit}";
        }
        return $this->makeRequest($endpoint);
    }

    public function getAllProducts(): array
    {

        return $this->makeRequest("/products?limit={$this->limitProducts}");
    }

    private function makeRequest(string $endpoint): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->retry($this->retries, 1000)
                ->get($this->baseUrl . $endpoint);

            if ($response->successful()) {
                return $response->json();
            }

            $this->handleHttpError($response->status());
        } catch (ConnectionException $e) {
            throw new BusinessException(
                'Upstream timeout',
                504,
                'integration.timeout',
                ['endpoint' => $endpoint]
            );
        } catch (RequestException $e) {
            if ($e->response->serverError()) {
                throw new BusinessException(
                    'Upstream error',
                    502,
                    'integration.upstream_error',
                    ['endpoint' => $endpoint, 'status' => $e->response->status()]
                );
            }

            throw new BusinessException(
                'Upstream request error',
                424,
                'integration.upstream_request',
                ['endpoint' => $endpoint, 'status' => $e->response->status()]
            );
        }
    }

    private function handleHttpError(int $status): void
    {
        if ($status >= 500) {
            throw new BusinessException(
                'Upstream error',
                502,
                'integration.upstream_error',
                ['status' => $status]
            );
        }

        throw new BusinessException(
            'Upstream request error',
            424,
            'integration.upstream_request',
            ['status' => $status]
        );
    }
}
