<?php

namespace Quark\Migrations;

class Config
{
    private static ?Config $instance = null;
    private static string|null $migrationsFilesDirectory;

    private function __construct(string $migrationsFilesDirectory)
    {
        static::$migrationsFilesDirectory = $migrationsFilesDirectory;
    }

    public static function getInstance(string $logFile = 'logs/orm.log'): Config
    {
        if (self::$instance === null) {
            self::$instance = new self($logFile);
        }
        return self::$instance;
    }

    public static function migrationsFilesDirectory(): string|null
    {
        static::getInstance();
        return static::$migrationsFilesDirectory;
    }
}
