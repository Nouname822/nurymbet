<?php

/** ========================================
 *
 *
 *! Файл: Supplier.php
 ** Директория: alpha\src\Supplier.php
 *? Цель: Поставщик поставляет все маршруты
 * Создано: 2025-03-29 08:46:10
 *
 *
============================================ */

namespace Nurymbet\Route\Core;

class Supplier
{
    public static function routes(): array
    {
        $config = Config::getInstance();
        return json_decode(Cache::get($config->get('cache_keys.routes_array', '')), true) ?? [];
    }
}
