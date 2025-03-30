<?php

namespace Nurymbet\Route\Helpers;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Nurymbet\Route\Core\Config;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileWatcher
{
    private static RedisAdapter $cache;

    public static function init(string $namespace = 'file_watcher', int $ttl = 86400): void
    {
        if (!isset(static::$cache)) {
            $config = Config::getInstance();
            $redisClient = $config->get('redis', null);
            if ($redisClient) {
                static::$cache = new RedisAdapter($redisClient, $namespace, $ttl);
            } else {
                throw new Exception('Redis клиент не верный или пустой настройте конфигурацию');
            }
        }
    }

    public static function getLastModified(string $path): int
    {
        if (!file_exists($path)) {
            return 0;
        }

        if (is_file($path)) {
            return filemtime($path);
        }

        $latestTime = 0;
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $latestTime = max($latestTime, $file->getMTime());
            }
        }

        return $latestTime;
    }

    public static function hasChanged(string $path): bool
    {
        static::init();
        $cacheKey = md5($path);
        $lastModified = static::getLastModified($path);

        $cachedTime = static::$cache->get($cacheKey, function (ItemInterface $item) use ($lastModified) {
            $item->expiresAfter(86400);
            return $lastModified;
        });

        return $cachedTime !== $lastModified;
    }

    public static function updateCache(string $path): void
    {
        static::init();
        $cacheKey = md5($path);
        $item = static::$cache->getItem($cacheKey);
        $item->set(static::getLastModified($path));
        $item->expiresAfter(86400);
        static::$cache->save($item);
    }
}
