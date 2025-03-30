<?php

namespace Quark\Sql;

class BUILD
{
    private static string $table;
    private static SQL $query;
    private static array $param = [];

    public static function table(string $table): static
    {
        static::$table = $table;
        return new static();
    }

    public static function insert(array $data): static
    {
        static::$query = SQL::insert(static::$table)::oBracket()::custom(implode(', ', array_keys($data)))::cBracket()::values(SQL::placeholders($data));
        return new static();
    }

    public static function update(array $data): static
    {
        static::$query = SQL::update(static::$table)::set(SQL::placeholders($data, 3));
        return new static();
    }

    public static function delete(): static
    {
        static::$query = SQL::delete(static::$table);
        return new static();
    }

    public static function select(string $cols): static
    {
        static::$query = SQL::select($cols)::from(static::$table);
        return new static();
    }

    public static function where(array $condition): static
    {
        $i = 0;
        foreach ($condition as $value) {
            if (count($value) === 3) {
                $placeholder = $value[0] . '_' . $i;
                static::$query = SQL::where($value[0] . '@' . $value[1] . '@:' . $placeholder);
                static::$param += [
                    $placeholder => $value[2]
                ];
                $i++;
            }
        }
        return new static();
    }

    public static function whereIn(array $condition, string $separator = 'and'): static
    {
        foreach ($condition as $key => $values) {
            $i = 0;
            $param = [];
            $placeholder = [];
            foreach ($values as $data) {
                $param[$key . '_' . $i] = $data;
                $placeholder[':' . $key . '_' . $i] = $data;
                $i++;
            }
        }
        static::$query = SQL::where(separator: $separator)::in($key, implode(', ', array_keys($placeholder)));
        static::$param += $param;
        return new static();
    }

    public static function toSql(): string
    {
        return static::$query::query();
    }

    public static function getParam(): array
    {
        return static::$param;
    }
}
