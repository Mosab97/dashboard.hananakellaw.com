<?php

namespace App\Services\API\Logger;

use Illuminate\Support\Facades\Log;

class AutoAbsenceLogger
{
    private static function log($level, $message, array $context = [])
    {
        $context['timestamp'] = now()->format('Y-m-d H:i:s.u');
        Log::channel('auto_absence')->$level($message, $context);
    }

    public static function info($message, array $context = [])
    {
        self::log('info', $message, $context);
    }

    public static function error($message, array $context = [])
    {
        self::log('error', $message, $context);
    }

    public static function warning($message, array $context = [])
    {
        self::log('warning', $message, $context);
    }

    public static function debug($message, array $context = [])
    {
        self::log('debug', $message, $context);
    }
}
