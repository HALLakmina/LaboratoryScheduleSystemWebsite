<?php
namespace Backend\Utils;

class Logger {
    private static $logDir = __DIR__ . '/../logs';

    private static function write(string $level, string $message, array $context = []): void {
        $dir = self::$logDir;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');
        $date = date('Y-m-d');
        $contextStr = empty($context) ? '' : ' | ' . json_encode($context);
        $line = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;

        file_put_contents("{$dir}/{$level} {$date}.log", $line, FILE_APPEND | LOCK_EX);
    }

    public static function error(string $message, array $context = []): void {
        self::write('error', $message, $context);
    }

    public static function warning(string $message, array $context = []): void {
        self::write('warning', $message, $context);
    }

    public static function info(string $message, array $context = []): void {
        self::write('info', $message, $context);
    }
}
?>
