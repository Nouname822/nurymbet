<?php

/** ========================================
 *
 *
 *! Файл: Save.php
 ** Директория: alpha\src\Auth\Save.php
 *? Цель: Для сохранение маршрутов
 *? Описание: 
 * Создано: 2025-03-29 08:46:54
 *
 *
============================================ */

namespace Nurymbet\Route\Auth;

use Nurymbet\Route\Core\Cache;
use Nurymbet\Route\Core\Config;
use Nurymbet\Route\Helpers\FileWatcher;

class Save
{
    public static function save(array $routes, array $names, array $callerFiles): void
    {
        $config = Config::getInstance();


        $cacheKey = $config->get('cache_keys.routes_array');
        $cacheNamesKey = $config->get('cache_keys.routes_name_array');

        if (!empty($callerFiles)) {
            foreach ($callerFiles as $path) {
                if (FileWatcher::hasChanged($path)) {
                    self::updateCache($cacheKey, $cacheNamesKey, $path, $routes, $names);
                    break;
                }
            }
        }
    }

    private static function updateCache(string $cacheKey, string $cacheNamesKey, string $path, array $routes, array $names): void
    {
        Cache::set($cacheKey, json_encode($routes), 86400);
        Cache::set($cacheNamesKey, json_encode($names), 86400);
        FileWatcher::updateCache($path);
    }
}
