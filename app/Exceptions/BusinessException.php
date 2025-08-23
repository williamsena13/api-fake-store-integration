<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BusinessException extends Exception
{
    protected int $status;
    protected string $code;
    protected array $context;

    public function __construct(
        string $message,
        int $status = 400,
        string $code = 'business.error',
        array $context = []
    ) {
        parent::__construct($message);
        $this->status = $status;
        $this->code = $code;
        $this->context = $context;
    }

    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => $this->code,
                'message' => $this->getMessage(),
                'status' => $this->status,
                'context' => $this->context,
                'request_id' => $request->header('X-Request-Id')
            ]
        ], $this->status);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}