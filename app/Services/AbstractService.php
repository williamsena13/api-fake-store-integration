<?php

namespace App\Services;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class AbstractService
{
    protected $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    protected function withTransaction(callable $callback)
    {
        return DB::transaction($callback);
    }

    protected function findOrFailBusiness(int $id, string $message = 'Resource not found')
    {
        $resource = $this->repository->getById($id);

        if (empty($resource)) {
            throw new BusinessException($message, 404, 'resource.not_found', ['id' => $id]);
        }

        return $resource;
    }

    protected function sanitizeFilters(array $params): array
    {
        $allowedFilters = $this->getAllowedFilters();

        return array_intersect_key($params, array_flip($allowedFilters));
    }

    protected function getAllowedFilters(): array
    {
        return ['page', 'per_page', 'order_by', 'order'];
    }

    protected function validatePagination(array $params): array
    {
        $params['per_page'] = min($params['per_page'] ?? 15, 100);
        $params['page'] = max($params['page'] ?? 1, 1);

        return $params;
    }

    protected function logError(\Exception $e, string $context = '')
    {
        Log::error("Service Error: {$context}", [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
