<?php

/** ========================================
 *
 *
 *! Файл: Request.php
 ** Директория: alpha\src\Request.php
 *? Цель: Нужен для получение данных запроса(маршрут, метод, user agent и т.д.)
 *? Описание: При настройке маршрута указываются данные запроса
 * Создано: 2025-03-29 08:43:57
 *
 *
============================================ */

namespace Nurymbet\Route\Http;

class Request
{
    private static ?self $instance = null;
    private array $config = [];

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function set(string $key, mixed $value): void
    {
        $this->config[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }
}
