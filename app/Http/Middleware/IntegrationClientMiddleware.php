<?php

namespace App\Http\Middleware;

use App\Exceptions\BusinessException;
use App\Logging\IntegrationLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IntegrationClientMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $clientId = $request->header('X-Client-Id');
        if (empty($clientId))
            throw new BusinessException(
                'Missing X-Client-Id',
                400,
                'integration.missing_client_id'
            );

        $requestId = $request->header('X-Request-Id') ?: Str::uuid()->toString();
        $request->headers->set('X-Request-Id', $requestId);
        $response = $next($request);
        $response->headers->set('X-Request-Id', $requestId);

        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        IntegrationLogger::info('Integration Request', [
            'request_id' => $requestId,
            'client_id' => $clientId,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'duration_ms' => $duration,
            'status_code' => $response->getStatusCode(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return $response;
    }
}
