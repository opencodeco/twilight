<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\Database\Contract;

use PDOStatement;

interface DatabaseContract
{

    public function execute(string $query, array $params = []): PDOStatement;
}
