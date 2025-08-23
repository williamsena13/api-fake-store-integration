<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;

class IntegrationLogger
{
    public static function info(string $message, array $context = []): void
    {
        $channel = self::getChannel();
        Log::channel($channel)->info($message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        $channel = self::getChannel();
        Log::channel($channel)->error($message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        $channel = self::getChannel();
        Log::channel($channel)->warning($message, $context);
    }

    private static function getChannel(): string
    {
        if (env('LOG_INTEGRATION_SLACK', false)) {
            return 'integration_slack';
        }
        
        return env('LOG_INTEGRATION_CHANNEL', 'integration');
    }
}