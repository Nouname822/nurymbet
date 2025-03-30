<?php

namespace Quark\Common;

use Quark\Cache\Manager;
use Quark\Sql\Exec;

class DB
{
    private static string $table;
    private static Exec $query;
    private static Manager $cache;
    private static array $cacheKeys = [];

    public static function init(): void
    {
        if (!isset(static::$cache)) {
            static::$cache = new Manager('file', 'request_cache', (3600 * 24) * 7);
        }
    }

    public static function table(string $table): static
    {
        static::$table = $table;
        return new static();
    }

    public static function create(array $data): static
    {
        static::$query = Exec::table(static::$table)::insert($data);
        return new static();
    }

    public static function update(array $data): static
    {
        static::$query = Exec::table(static::$table)::update($data);
        return new static();
    }

    public static function delete(array $ids): static
    {
        static::$query = Exec::table(static::$table)::delete($ids);
        return new static();
    }

    public static function select(string $fields): static
    {
        static::init();
        $cacheKey = 'select_request_' . static::$table . $fields;
        static::$cacheKeys[] = $cacheKey;
        static::$query = Exec::table(static::$table)::select($fields);
        return new static();
    }

    public static function where(array $condition): static
    {
        static::$query = Exec::where($condition);
        return new static();
    }

    public static function whereIn(array $condition): static
    {
        static::$query = Exec::whereIn($condition);
        return new static();
    }

    public static function get()
    {
        static::init();
        $result = static::$query::fetch();
        foreach (static::$cacheKeys as $cacheKey) {
            static::$cache->set($cacheKey, json_encode($result));
        }
        return $result;
    }
}
