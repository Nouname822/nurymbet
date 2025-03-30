<?php

namespace Quark\Sql;

use Exception;
use Quark\Cache\Manager;
use Quark\Logs\Log;

class SQL
{
    private static $query = '';
    private static $isFirstWhere = true;

    private static function isolation(string|int|float|bool $str): string
    {
        if ($str === false) {
            $str = 0;
        }

        $escaped = (string) $str;

        return "@{$escaped}@";
    }

    private static function checkField(string|int|float|bool $query): string
    {
        $cache = new Manager(namespace: "custom_field_check");

        if (!$cache->has((string)$query)) {
            try {
                foreach (Dictionary::cases() as $sqlKeyword) {
                    $pattern = '/\b' . preg_quote($sqlKeyword->value, '/') . '\b/i';

                    if (preg_match($pattern, $query)) {
                        throw new Exception("Ошибка: использование SQL-оператора '{$sqlKeyword->value}' запрещено!");
                    }
                }
            } catch (Exception $e) {
                $trace = $e->getTrace();
                Log::error($e->getMessage() . ' Файл: ' . $trace[0]['file'] . ', Линия: ' . $trace[0]['line'] . ', Метод: ' . $trace[0]['function'] . ', Класс: ' . $trace[0]['class']);
                exit;
            }

            $cache->set((string)$query, '', 86400);
        }

        return static::isolation(str_replace("'", "\'", $query));
    }

    /**
     * Получение плейсхолдеров
     *
     * @param array $data ['key' => 'value']
     * @param integer $mode 1, 2, 3
     * @return string $mode 1 - :key, 2 - key, 3 - key = :key
     */
    public static function placeholders(array $data, int $mode = 1): string
    {
        $placeholders = array_map(fn($key) => ":$key", array_keys($data));
        $placeholdersWithoutColon = array_map(fn($key) => "$key", array_keys($data));

        return match ($mode) {
            1 => implode(', ', $placeholders),
            2 => implode(', ', $placeholdersWithoutColon),
            3 => implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)))
        };
    }

    /**
     * Получение условий
     *
     * @param array $conditions [['field', 'operator', 'value']]
     * @return array ['query' => ['field operator :field1'], 'params' => ['field1' => 'value']]
     */
    public static function conditions(array $conditions): array
    {
        $queryParts = [];
        $params = [];
        $counter = 1;

        foreach ($conditions as [$field, $operator, $value]) {
            if (!is_string($field) || !is_string($operator)) {
                Log::error('Некорректный формат условия');
                exit;
            }

            $placeholder = $field . '_' . $counter;
            $queryParts[] = "$field $operator :$placeholder";
            $params[$placeholder] = $value;

            $counter++;
        }

        return ['query' => $queryParts, 'params' => $params];
    }


    /**
     * Получение условий с плейсхолдерами
     *
     * @param array $data ['key' => ['value1', 'value2', 'value3']]
     * @return array ['query' => ['key_0', 'key_1', 'key_2'], 'params' => ['key_0' => 'value1', 'key_1' => 'value2', 'key_2' => 'value3']]
     */
    public static function flatten(array $data): array
    {
        $placeholders = [];
        $params = [];

        foreach ($data as $key => $values) {
            foreach ((array) $values as $index => $value) {
                $placeholder = "{$key}_{$index}";
                $placeholders[$key][] = $placeholder;
                $params[$placeholder] = $value;
            }
        }

        foreach ($placeholders as &$group) {
            $grouped = [];
            foreach ($group as $value) {
                $grouped[] = ':' . $value;
            }
            $group = implode(', ', $grouped);
        }

        return ['query' => $placeholders, 'params' => $params];
    }







    public static function custom(string|int|float|bool $query): static
    {
        static::$query .= static::checkField($query);
        return new static();
    }
    public static function select(string $fields = '*'): static
    {
        static::$query = static::isolation(Dictionary::SELECT->value . '@' . static::checkField($fields));
        return new static();
    }
    public static function insert(string $table): static
    {
        static::$query .= static::isolation(Dictionary::INSERT_INTO->value . '@' . $table);
        return new static();
    }
    public static function update(string $table): static
    {
        static::$query .= static::isolation(Dictionary::UPDATE->value . '@' . $table);
        return new static();
    }
    public static function delete(string $table): static
    {
        static::$query .= static::isolation(Dictionary::DELETE->value . '@' . Dictionary::FROM->value . '@' . $table);
        return new static();
    }
    public static function everything(): static
    {
        static::$query .= static::isolation(Dictionary::EVERYTHING->value);
        return new static();
    }
    public static function truncate(string $table): static
    {
        static::$query .= static::isolation(Dictionary::TRUNCATE->value . ' ' . Dictionary::TABLE->value . ' ' . $table);
        return new static();
    }
    public static function from(string $from = ''): static
    {
        static::$query .= static::isolation(Dictionary::FROM->value . ' ' . static::checkField($from));
        return new static();
    }
    public static function where(string $condition = '', string $separator = 'and'): static
    {
        $spacer = Dictionary::AND->value;
        if (strtolower($separator) === 'or') {
            $spacer = Dictionary::OR->value;
        }
        if (static::$isFirstWhere) {
            static::$query .= static::isolation(Dictionary::WHERE->value . ' ' . $condition);
            static::$isFirstWhere = false;
        } else {
            static::$query .= static::isolation($spacer . ' ' . $condition);
        }
        return new static();
    }
    public static function whereNotExists(string $query = ''): static
    {
        if (static::$isFirstWhere) {
            static::$query .= static::isolation(Dictionary::WHERE->value . '@' . Dictionary::NOT->value . '@' . Dictionary::EXISTS->value . '@(' . $query . ')');
            static::$isFirstWhere = false;
        } else {
            static::$query .= static::isolation(Dictionary::AND->value . '@' . Dictionary::NOT->value . '@' . Dictionary::EXISTS->value . '@(' . $query . ')');
        }
        return new static();
    }
    public static function whereExists(string $query = ''): static
    {
        if (static::$isFirstWhere) {
            static::$query .= static::isolation(Dictionary::WHERE->value . '@' . Dictionary::EXISTS->value . '@(' . $query . ')');
            static::$isFirstWhere = false;
        } else {
            static::$query .= static::isolation(Dictionary::AND->value . '@' . Dictionary::EXISTS->value . '@(' . $query . ')');
        }
        return new static();
    }
    public static function set(string $field): static
    {
        static::$query .= static::isolation(Dictionary::SET->value . ' ' . $field);
        return new static();
    }
    public static function values(string $field): static
    {
        static::$query .= static::isolation(Dictionary::VALUES->value . ' (' . $field . ')');
        return new static();
    }
    public static function orderBy(string $field, string $sortBy = 'asc'): static
    {
        static::$query .= static::isolation(Dictionary::ORDER_BY->value . ' ' . $field . ' ' . strtoupper($sortBy));
        return new static();
    }
    public static function groupBy(string $field = ''): static
    {
        static::$query .= static::isolation(Dictionary::GROUP_BY->value . ' ' . static::checkField($field));
        return new static();
    }
    public static function having(): static
    {
        static::$query .= static::isolation(Dictionary::HAVING->value);
        return new static();
    }
    public static function limit(int $limit): static
    {
        static::$query .= static::isolation(Dictionary::LIMIT->value . ' ' . $limit);
        return new static();
    }
    public static function offset(int $offset): static
    {
        static::$query .= static::isolation(Dictionary::OFFSET->value . ' ' . $offset);
        return new static();
    }
    public static function distinct(string $field = ''): static
    {
        static::$query .= static::isolation(Dictionary::DISTINCT->value . ' ' . $field);
        return new static();
    }
    public static function join(): static
    {
        static::$query .= static::isolation(Dictionary::JOIN->value);
        return new static();
    }
    public static function innerJoin(string $table): static
    {
        static::$query .= static::isolation(Dictionary::INNER_JOIN->value . '@' . $table . '@' . Dictionary::ON->value);
        return new static();
    }
    public static function leftJoin(string $table): static
    {
        static::$query .= static::isolation(Dictionary::LEFT_JOIN->value . '@' . $table . '@' . Dictionary::ON->value);
        return new static();
    }

    public static function rightJoin(string $table): static
    {
        static::$query .= static::isolation(Dictionary::RIGHT_JOIN->value . '@' . $table . '@' . Dictionary::ON->value);
        return new static();
    }

    public static function fullJoin(): static
    {
        static::$query .= static::isolation(Dictionary::FULL_JOIN->value);
        return new static();
    }

    public static function on(string $field = ''): static
    {
        static::$query .= static::isolation(Dictionary::ON->value . '@' . $field);
        return new static();
    }

    public static function as(string $alias): static
    {
        static::$query .= static::isolation(Dictionary::AS->value . '@' . $alias);
        return new static();
    }

    public static function in(string $field, string $fields): static
    {
        static::$query .= static::isolation($field . '@' . Dictionary::IN->value . '@(' . $fields . ')');

        return new static();
    }

    public static function notIn(string $field, string $fields): static
    {
        static::$query .= static::isolation($field . '@' . Dictionary::NOT->value . '@' . Dictionary::IN->value . '@(' . $fields . ')');

        return new static();
    }


    public static function between(int|string $x, int|string $y): static
    {
        static::$query .= static::isolation(Dictionary::BETWEEN->value . '@' . $x . '@' . Dictionary::AND->value . '@' . $y);
        return new static();
    }

    public static function notBetween(int|string $x, int|string $y): static
    {
        static::$query .= static::isolation(Dictionary::NOT->value . '@' . Dictionary::BETWEEN->value . '@' . $x . '@' . Dictionary::AND->value . '@' . $y);
        return new static();
    }
    public static function like(string $field): static
    {
        static::$query .= static::isolation(Dictionary::LIKE->value . '@' . "" . $field . "");
        return new static();
    }

    public static function isNull(): static
    {
        static::$query .= static::isolation(Dictionary::IS_NULL->value);
        return new static();
    }

    public static function isNotNull(): static
    {
        static::$query .= static::isolation(Dictionary::IS_NOT_NULL->value);
        return new static();
    }

    public static function union(): static
    {
        static::$query .= static::isolation(Dictionary::UNION->value);
        return new static();
    }

    public static function unionAll(): static
    {
        static::$query .= static::isolation(Dictionary::UNION_ALL->value);
        return new static();
    }

    public static function case(): static
    {
        static::$query .= static::isolation(Dictionary::CASE->value);
        return new static();
    }

    public static function when(string $condition): static
    {
        static::$query .= static::isolation(Dictionary::WHEN->value . '@' . $condition);
        return new static();
    }

    public static function then(string $then): static
    {
        static::$query .= static::isolation(Dictionary::THEN->value . '@' . $then);
        return new static();
    }

    public static function else(string $else): static
    {
        static::$query .= static::isolation(Dictionary::ELSE->value . '@' . $else);
        return new static();
    }

    public static function end(): static
    {
        static::$query .= static::isolation(Dictionary::END->value);
        return new static();
    }
    public static function count(string $fields = '*'): static
    {
        static::$query .= static::isolation(Dictionary::COUNT->value . '@(' . $fields . ')');
        return new static();
    }

    public static function sum(string $sum): static
    {
        static::$query .= static::isolation(Dictionary::SUM->value . '@(' . $sum . ')');
        return new static();
    }

    public static function avg(string $field): static
    {
        static::$query .= static::isolation(Dictionary::AVG->value . '@(' . $field . ')');
        return new static();
    }

    public static function max(string $field): static
    {
        static::$query .= static::isolation(Dictionary::MAX->value . '@(' . $field . ')');
        return new static();
    }

    public static function min(string $field): static
    {
        static::$query .= static::isolation(Dictionary::MIN->value . '@(' . $field . ')');
        return new static();
    }

    public static function and(): static
    {
        static::$query .= static::isolation(Dictionary::AND->value);
        return new static();
    }

    public static function or(): static
    {
        static::$query .= static::isolation(Dictionary::OR->value);
        return new static();
    }

    public static function not(): static
    {
        static::$query .= static::isolation(Dictionary::NOT->value);
        return new static();
    }

    public static function xor(): static
    {
        static::$query .= static::isolation(Dictionary::XOR->value);
        return new static();
    }

    public static function asc(): static
    {
        static::$query .= static::isolation(Dictionary::ASC->value);
        return new static();
    }

    public static function desc(): static
    {
        static::$query .= static::isolation(Dictionary::DESC->value);
        return new static();
    }

    public static function exists(): static
    {
        static::$query .= static::isolation(Dictionary::EXISTS->value);
        return new static();
    }

    public static function notExists(): static
    {
        static::$query .= static::isolation(Dictionary::NOT_EXISTS->value);
        return new static();
    }

    public static function any(): static
    {
        static::$query .= static::isolation(Dictionary::ANY->value);
        return new static();
    }

    public static function all(): static
    {
        static::$query .= static::isolation(Dictionary::ALL->value);
        return new static();
    }

    public static function createTable(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_TABLE->value);
        return new static();
    }

    public static function createIndex(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_INDEX->value);
        return new static();
    }

    public static function createView(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_VIEW->value);
        return new static();
    }

    public static function createTrigger(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_TRIGGER->value);
        return new static();
    }

    public static function createProcedure(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_PROCEDURE->value);
        return new static();
    }

    public static function createFunction(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_FUNCTION->value);
        return new static();
    }

    public static function createSchema(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_SCHEMA->value);
        return new static();
    }

    public static function createDatabase(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_DATABASE->value);
        return new static();
    }

    public static function createSequence(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_SEQUENCE->value);
        return new static();
    }

    public static function alterTable(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_TABLE->value);
        return new static();
    }

    public static function alterColumn(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_COLUMN->value);
        return new static();
    }

    public static function alterIndex(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_INDEX->value);
        return new static();
    }

    public static function alterView(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_VIEW->value);
        return new static();
    }

    public static function alterTrigger(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_TRIGGER->value);
        return new static();
    }

    public static function alterProcedure(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_PROCEDURE->value);
        return new static();
    }

    public static function alterFunction(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_FUNCTION->value);
        return new static();
    }

    public static function alterDatabase(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_DATABASE->value);
        return new static();
    }

    public static function alterSchema(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_SCHEMA->value);
        return new static();
    }

    public static function alterSequence(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_SEQUENCE->value);
        return new static();
    }

    public static function dropTable(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_TABLE->value);
        return new static();
    }

    public static function dropIndex(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_INDEX->value);
        return new static();
    }

    public static function dropView(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_VIEW->value);
        return new static();
    }

    public static function dropTrigger(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_TRIGGER->value);
        return new static();
    }

    public static function dropProcedure(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_PROCEDURE->value);
        return new static();
    }

    public static function dropFunction(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_FUNCTION->value);
        return new static();
    }

    public static function dropDatabase(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_DATABASE->value);
        return new static();
    }

    public static function dropSchema(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_SCHEMA->value);
        return new static();
    }

    public static function dropSequence(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_SEQUENCE->value);
        return new static();
    }

    public static function renameTable(): static
    {
        static::$query .= static::isolation(Dictionary::RENAME_TABLE->value);
        return new static();
    }

    public static function renameColumn(): static
    {
        static::$query .= static::isolation(Dictionary::RENAME_COLUMN->value);
        return new static();
    }

    public static function addColumn(): static
    {
        static::$query .= static::isolation(Dictionary::ADD_COLUMN->value);
        return new static();
    }

    public static function dropColumn(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_COLUMN->value);
        return new static();
    }

    public static function modifyColumn(): static
    {
        static::$query .= static::isolation(Dictionary::MODIFY_COLUMN->value);
        return new static();
    }

    public static function changeColumn(): static
    {
        static::$query .= static::isolation(Dictionary::CHANGE_COLUMN->value);
        return new static();
    }

    public static function addConstraint(): static
    {
        static::$query .= static::isolation(Dictionary::ADD_CONSTRAINT->value);
        return new static();
    }

    public static function dropConstraint(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_CONSTRAINT->value);
        return new static();
    }

    public static function primaryKey(): static
    {
        static::$query .= static::isolation(Dictionary::PRIMARY_KEY->value);
        return new static();
    }

    public static function foreignKey(): static
    {
        static::$query .= static::isolation(Dictionary::FOREIGN_KEY->value);
        return new static();
    }

    public static function unique(): static
    {
        static::$query .= static::isolation(Dictionary::UNIQUE->value);
        return new static();
    }

    public static function check(): static
    {
        static::$query .= static::isolation(Dictionary::CHECK->value);
        return new static();
    }

    public static function default(): static
    {
        static::$query .= static::isolation(Dictionary::DEFAULT->value);
        return new static();
    }

    public static function index(): static
    {
        static::$query .= static::isolation(Dictionary::INDEX->value);
        return new static();
    }

    public static function fulltextIndex(): static
    {
        static::$query .= static::isolation(Dictionary::FULLTEXT_INDEX->value);
        return new static();
    }

    public static function spatialIndex(): static
    {
        static::$query .= static::isolation(Dictionary::SPATIAL_INDEX->value);
        return new static();
    }

    public static function autoIncrement(): static
    {
        static::$query .= static::isolation(Dictionary::AUTO_INCREMENT->value);
        return new static();
    }

    public static function generatedAlways(): static
    {
        static::$query .= static::isolation(Dictionary::GENERATED_ALWAYS->value);
        return new static();
    }

    public static function comment(): static
    {
        static::$query .= static::isolation(Dictionary::COMMENT->value);
        return new static();
    }

    public static function engine(): static
    {
        static::$query .= static::isolation(Dictionary::ENGINE->value);
        return new static();
    }

    public static function collate(): static
    {
        static::$query .= static::isolation(Dictionary::COLLATE->value);
        return new static();
    }

    public static function charset(): static
    {
        static::$query .= static::isolation(Dictionary::CHARSET->value);
        return new static();
    }

    public static function tablespace(): static
    {
        static::$query .= static::isolation(Dictionary::TABLESPACE->value);
        return new static();
    }

    public static function replace(): static
    {
        static::$query .= static::isolation(Dictionary::REPLACE->value);
        return new static();
    }

    public static function merge(): static
    {
        static::$query .= static::isolation(Dictionary::MERGE->value);
        return new static();
    }

    public static function call(): static
    {
        static::$query .= static::isolation(Dictionary::CALL->value);
        return new static();
    }

    public static function lockTables(): static
    {
        static::$query .= static::isolation(Dictionary::LOCK_TABLES->value);
        return new static();
    }

    public static function unlockTables(): static
    {
        static::$query .= static::isolation(Dictionary::UNLOCK_TABLES->value);
        return new static();
    }

    public static function analyzeTable(): static
    {
        static::$query .= static::isolation(Dictionary::ANALYZE_TABLE->value);
        return new static();
    }

    public static function checkTable(): static
    {
        static::$query .= static::isolation(Dictionary::CHECK_TABLE->value);
        return new static();
    }

    public static function optimizeTable(): static
    {
        static::$query .= static::isolation(Dictionary::OPTIMIZE_TABLE->value);
        return new static();
    }

    public static function repairTable(): static
    {
        static::$query .= static::isolation(Dictionary::REPAIR_TABLE->value);
        return new static();
    }

    public static function showTables(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_TABLES->value);
        return new static();
    }

    public static function showColumns(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_COLUMNS->value);
        return new static();
    }

    public static function showIndexes(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_INDEXES->value);
        return new static();
    }

    public static function showDatabases(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_DATABASES->value);
        return new static();
    }

    public static function showStatus(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_STATUS->value);
        return new static();
    }

    public static function showVariables(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_VARIABLES->value);
        return new static();
    }

    public static function showWarnings(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_WARNINGS->value);
        return new static();
    }

    public static function showErrors(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_ERRORS->value);
        return new static();
    }

    public static function showCreateTable(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_CREATE_TABLE->value);
        return new static();
    }

    public static function showCreateDatabase(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_CREATE_DATABASE->value);
        return new static();
    }

    public static function showTriggers(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_TRIGGERS->value);
        return new static();
    }

    public static function showEvents(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_EVENTS->value);
        return new static();
    }

    public static function showProcesslist(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_PROCESSLIST->value);
        return new static();
    }

    public static function flush(): static
    {
        static::$query .= static::isolation(Dictionary::FLUSH->value);
        return new static();
    }

    public static function reset(): static
    {
        static::$query .= static::isolation(Dictionary::RESET->value);
        return new static();
    }

    public static function cacheIndex(): static
    {
        static::$query .= static::isolation(Dictionary::CACHE_INDEX->value);
        return new static();
    }

    public static function loadIndex(): static
    {
        static::$query .= static::isolation(Dictionary::LOAD_INDEX->value);
        return new static();
    }

    public static function resetQueryCache(): static
    {
        static::$query .= static::isolation(Dictionary::RESET_QUERY_CACHE->value);
        return new static();
    }

    public static function kill(): static
    {
        static::$query .= static::isolation(Dictionary::KILL->value);
        return new static();
    }

    public static function createUser(): static
    {
        static::$query .= static::isolation(Dictionary::CREATE_USER->value);
        return new static();
    }

    public static function dropUser(): static
    {
        static::$query .= static::isolation(Dictionary::DROP_USER->value);
        return new static();
    }

    public static function alterUser(): static
    {
        static::$query .= static::isolation(Dictionary::ALTER_USER->value);
        return new static();
    }

    public static function renameUser(): static
    {
        static::$query .= static::isolation(Dictionary::RENAME_USER->value);
        return new static();
    }

    public static function grant(): static
    {
        static::$query .= static::isolation(Dictionary::GRANT->value);
        return new static();
    }

    public static function revoke(): static
    {
        static::$query .= static::isolation(Dictionary::REVOKE->value);
        return new static();
    }

    public static function showGrants(): static
    {
        static::$query .= static::isolation(Dictionary::SHOW_GRANTS->value);
        return new static();
    }

    public static function startTransaction(): static
    {
        static::$query .= static::isolation(Dictionary::START_TRANSACTION->value);
        return new static();
    }

    public static function commit(): static
    {
        static::$query .= static::isolation(Dictionary::COMMIT->value);
        return new static();
    }

    public static function rollback(): static
    {
        static::$query .= static::isolation(Dictionary::ROLLBACK->value);
        return new static();
    }

    public static function savepoint(): static
    {
        static::$query .= static::isolation(Dictionary::SAVEPOINT->value);
        return new static();
    }

    public static function releaseSavepoint(): static
    {
        static::$query .= static::isolation(Dictionary::RELEASE_SAVEPOINT->value);
        return new static();
    }

    public static function setTransaction(): static
    {
        static::$query .= static::isolation(Dictionary::SET_TRANSACTION->value);
        return new static();
    }

    public static function oBracket(): static
    {
        static::$query .= static::isolation('(');
        return new static();
    }

    public static function cBracket(): static
    {
        static::$query .= static::isolation(')');
        return new static();
    }






    public static function query(): string
    {
        $query = static::$query;
        $query = trim($query, '@');
        $query = str_replace('@', ' ', $query);
        $query = preg_replace('/\s+/', ' ', $query);
        static::$query = '';
        static::$isFirstWhere = true;
        return trim($query);
    }
}
