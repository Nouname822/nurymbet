<?php

/** ========================================
 *
 *
 *! Файл: Cache.php
 ** Директория: alpha\src\Cache.php
 *? Цель: Для удобной работы с кэшами Symfony/Cache
 * Создано: 2025-03-29 08:31:52
 *
 *
============================================ */

namespace Nurymbet\Route\Core;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class Cache
{
    private static ?FilesystemAdapter $cache = null;
    private static int $defaultTtl = 3600;

    /**
     * Инициализация, получение с конфига местоположение папки с кэшем
     *
     * @return void
     */
    private static function init(): void
    {
        if (self::$cache === null) {
            $config = Config::getInstance();
            self::$cache = new FilesystemAdapter('route_cache', self::$defaultTtl, $config->get('cache_dir'));
        }
    }

    /**
     * Получение данных с кэша
     *
     * @param string $key
     * @param callable|null|null $callback
     * @param integer|null|null $ttl
     * @return mixed
     */
    public static function get(string $key, callable|null $callback = null, int|null $ttl = null): mixed
    {
        self::init();
        return self::$cache->get($key, function (ItemInterface $item) use ($callback, $ttl) {
            $item->expiresAfter($ttl ?? self::$defaultTtl);
            return $callback ? $callback() : null;
        });
    }

    /**
     * Добавить/Обновить данные кэша
     *
     * @param string $key
     * @param mixed $value
     * @param integer|null|null $ttl
     * @return void
     */
    public static function set(string $key, mixed $value, int|null $ttl = null): void
    {
        self::init();
        $item = self::$cache->getItem($key);
        $item->set($value);
        $item->expiresAfter($ttl ?? self::$defaultTtl);
        self::$cache->save($item);
    }


    /**
     * Проверка на существование данных в кэше
     *
     * @param string $key
     * @return boolean
     */
    public static function has(string $key): bool
    {
        self::init();
        return self::$cache->hasItem($key);
    }


    /**
     * Удаление данных кэша
     *
     * @param string $key
     * @return void
     */
    public static function del(string $key): void
    {
        self::init();
        self::$cache->deleteItem($key);
    }


    /**
     * Очистка всего кэша
     *
     * @return void
     */
    public static function clear(): void
    {
        self::init();
        self::$cache->clear();
    }

    /**
     * Смена времени хранение кэша
     *
     * @param integer $ttl
     * @return void
     */
    public static function setDefaultTtl(int $ttl): void
    {
        self::$defaultTtl = $ttl;
    }
}
