<?php

namespace Quark\Sql;

use PDO;
use PDOException;
use Quark\Common\Connect;

class Exec
{
    private static string $table;
    private static BUILD $query;
    private static array $param = [];

    public static function table(string $table): static
    {
        static::$table = $table;
        return new static();
    }

    public static function insert(array $data): static
    {
        static::$query = BUILD::table(static::$table)::insert($data);
        static::$param += $data;
        return new static();
    }

    public static function update(array $data): static
    {
        static::$query = BUILD::table(static::$table)::update($data);
        static::$param += $data;
        return new static();
    }

    public static function delete(array $condition): static
    {
        static::$query = BUILD::table(static::$table)::delete()::whereIn(['id' => $condition]);
        static::$param += BUILD::getParam();
        return new static();
    }

    public static function select(string $cols = '*'): static
    {
        static::$query = BUILD::table(static::$table)::select($cols);
        return new static();
    }

    public static function where(array $condition): static
    {
        static::$query = BUILD::where($condition);
        static::$param += BUILD::getParam();
        return new static();
    }

    public static function whereIn(array $condition): static
    {
        static::$query = BUILD::whereIn($condition);
        static::$param += BUILD::getParam();
        return new static();
    }

    public static function fetch(): array
    {
        if (static::$query) {
            return static::query(static::$query::toSql(), static::$param);
        }
        return [
            'code' => '500',
            'message' => 'Нет запроса для выполнение. Нечего выполнить'
        ];
    }

    private static function query(string $query, array $data, int $fetchMethod = PDO::FETCH_ASSOC): array
    {
        try {
            $pdo = Connect::pdo();
            $stmt = $pdo->prepare($query);
            $stmt->execute($data);

            if (str_starts_with($query, Dictionary::SELECT->value)) {
                $result = $stmt->fetchAll($fetchMethod);

                if (count($result) === 1) {
                    $result = $result[0];
                }

                return [
                    'code' => '200',
                    'message' => 'Данные успешно получены',
                    'data' => $result
                ];
            }
            if (str_starts_with($query, Dictionary::INSERT_INTO->value)) {
                return [
                    'code' => '200',
                    'message' => 'Данные успешно добавлены',
                    'data' => $data
                ];
            }
            if (str_starts_with($query, Dictionary::UPDATE->value)) {
                return [
                    'code' => '200',
                    'message' => 'Данные успешно обновлены',
                    'data' => $data
                ];
            }
            if (str_starts_with($query, Dictionary::DELETE->value)) {
                return [
                    'code' => '200',
                    'message' => 'Данные успешно удалены',
                    'data' => $data
                ];
            }
            return [
                'code' => '200',
                'message' => 'Запрос успешно выполнен',
                'data' => $data
            ];
        } catch (PDOException $e) {
            return [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
        return [
            'code' => '500',
            'message' => 'Неизвестная ошибка при выполнение запроса'
        ];
    }
}
