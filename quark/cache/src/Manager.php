<?php

namespace Quark\Cache;

use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class Manager
{
    private $cache;

    public function __construct(string $type = 'file', string $namespace = 'app_cache', int $lifetime = 3600)
    {
        switch ($type) {
            case 'redis':
                $redisConnection = RedisAdapter::createConnection('redis://localhost:6379');
                $this->cache = new RedisAdapter($redisConnection, $namespace, $lifetime);
                break;
            case 'memory':
                $this->cache = new ArrayAdapter($lifetime);
                break;
            case 'file':
            default:
                $this->cache = new FilesystemAdapter($namespace, $lifetime, __DIR__ . '/../var/cache');
                break;
        }
    }

    /**
     * Получение данных из кеша
     */
    public function get(string $key, callable|null $callback = null)
    {
        return $this->cache->get($key, function (ItemInterface $item) use ($callback) {
            $item->expiresAfter(3600); // Время жизни кеша (по умолчанию 1 час)
            return $callback ? $callback() : null;
        });
    }

    /**
     * Запись данных в кеш
     */
    public function set(string $key, mixed $value, int $lifetime = 3600): void
    {
        $item = $this->cache->getItem($key);
        $item->set($value);
        $item->expiresAfter($lifetime);
        $this->cache->save($item);
    }

    /**
     * Удаление данных из кеша по ключу
     */
    public function delete(string $key): void
    {
        $this->cache->deleteItem($key);
    }

    /**
     * Очистка всего кеша
     */
    public function clear(): void
    {
        $this->cache->clear();
    }

    /**
     * Проверка наличия кеша по ключу
     */
    public function has(string $key): bool
    {
        return $this->cache->hasItem($key);
    }
}
