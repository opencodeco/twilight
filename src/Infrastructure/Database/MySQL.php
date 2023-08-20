<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\Database;

use PDO;
use PDOStatement;
use Twilight\Infrastructure\Database\Contract\DatabaseContract;

class MySQL implements DatabaseContract
{
    private ?PDO $connection = null;

    public function __construct(
        protected readonly string $dsn,
        protected readonly string $username,
        protected readonly string $password
    ) {
    }

    public static function create(string $dsn, string $username, string $password): self
    {
        return new self($dsn, $username, $password);
    }

    protected function connect(): PDO
    {
        if ($this->connection === null) {
            $this->connection = new PDO($this->dsn, $this->username, $this->password);
        }
        return $this->connection;
    }

    public function execute(string $query, array $params = []): PDOStatement
    {
        $statement = $this->connect()->prepare($query);
        $statement->execute($params);
        return $statement;
    }
}
