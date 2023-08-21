<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Contracts;

use Twilight\Infrastructure\HTTP\Routing\Route;

interface ResolverContract
{
    public function append(string $method, string $uri, int $id): self;

    public function match(string $method, string $uri): ?Route;
}
