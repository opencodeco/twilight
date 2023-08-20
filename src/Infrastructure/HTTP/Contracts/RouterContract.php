<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Contracts;

interface RouterContract
{
    public function add(string $method, string $uri, callable $handler): static;

    public function __call(string $name, array $arguments);

    public function handle(string $method, string $uri): void;
}
