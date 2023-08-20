<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP;

readonly class Route
{
    public function __construct(
        public string $method,
        public string $uri,
        public array $params
    ) {
    }

    public static function make(string $method, string $uri, array $params): self
    {
        return new self($method, $uri, $params);
    }

    public function param(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }
}
