<?php

/** ========================================
 *
 *
 *! Файл: Generate.php
 ** Директория: alpha\src\Generate.php
 *? Цель: Для генерации URL используя название и наоборот
 * Создано: 2025-03-29 08:42:18
 *
 *
============================================ */

namespace Nurymbet\Route\Helpers;

use Nurymbet\Route\Core\Cache;
use Nurymbet\Route\Core\Config;

class Generate
{
    public static function url(string $name): string
    {
        $config = Config::getInstance();
        $routes = json_decode(Cache::get($config->get('cache_keys.routes_name_array', '')), true) ?? [];

        return $routes[$name] ?? '';
    }

    public static function name(string $url): string
    {
        $config = Config::getInstance();
        $routes = json_decode(Cache::get($config->get('cache_keys.routes_name_array', '')), true) ?? [];

        return array_search($url, $routes) ?? '';
    }
}
