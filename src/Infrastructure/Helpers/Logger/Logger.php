<?php

namespace src\Infrastructure\Helpers\Logger;

trait Logger
{
    public function createLog(string $message, string $source, string $status, string $phase = ''): void
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $logFilePath = storage_path('logs/bomi_' . now()->format('Y-m-d') . '.log');
        $logEntry = "\n\n[{$timestamp}] [{$source}] [{$status}] [START]";
        $logEntry.= "\n{$message}\n";
        $logEntry.= "[{$timestamp}] [{$source}] [{$status}] [END]";
        file_put_contents($logFilePath, $logEntry, FILE_APPEND);
    }
}
