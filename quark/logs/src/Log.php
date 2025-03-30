<?php

namespace Quark\Logs;

class Log
{
    private static ?Log $instance = null;
    private static string $logFile;

    private function __construct(string $logFile = 'logs/orm.log')
    {
        static::$logFile = $logFile;
    }

    public static function getInstance(string $logFile = 'logs/orm.log'): Log
    {
        if (self::$instance === null) {
            self::$instance = new self($logFile);
        }
        return self::$instance;
    }

    public static function log(string $level, string $message): void
    {
        static::getInstance();

        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;

        file_put_contents(static::$logFile, $logEntry, FILE_APPEND);
    }

    public static function info(string $message): void
    {
        static::log('INFO', $message);
    }

    public static function error(string $message): void
    {
        static::log('ERROR', $message);
    }

    public static function warning(string $message): void
    {
        static::log('WARNING', $message);
    }
}
