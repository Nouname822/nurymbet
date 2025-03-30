<?php

declare(strict_types=1);

namespace Quark\Common;

use Amp\Postgres\PostgresConfig;
use Amp\Postgres\PostgresConnection;
use Amp\Sql\SqlConnectionException;
use Amp\Sql\SqlQueryError;

use function Amp\Postgres\connect;

class Connect
{
    private ?PostgresConnection $connection = null;
    private PostgresConfig $config;

    public function __construct(string $host, int $port, string $database, string $user, string $password)
    {
        $this->config = PostgresConfig::fromString("host=$host port=$port dbname=$database user=$user password=$password");
    }

    public function connect(): void
    {
        try {
            $this->connection = connect($this->config);
        } catch (SqlConnectionException $e) {
            throw new \RuntimeException("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }

    public function query(string $sql, array $params = []): array
    {
        if (!$this->connection) {
            $this->connect();
        }

        try {
            $statement = $this->connection->prepare($sql);
            $result = $statement->execute($params);
            return $result->fetchRow();
        } catch (SqlQueryError $e) {
            throw new \RuntimeException("Ошибка выполнения запроса: " . $e->getMessage());
        }
    }

    public function getConnection(): ?PostgresConnection
    {
        return $this->connection;
    }
}
