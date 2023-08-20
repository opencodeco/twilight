<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP;

use JsonSerializable;
use Twilight\Infrastructure\HTTP\Contracts\RouteContract;

readonly class Route implements JsonSerializable, RouteContract
{
    public function __construct(
        protected string $method,
        protected string $uri,
        protected array $params
    ) {
    }

    public static function make(string $method, string $uri, array $params): self
    {
        return new self($method, $uri, $params);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function param(string $name): mixed
    {
        return $this->params[$name] ?? null;
    }

    public function jsonSerialize(): array
    {
        return [
            'method' => $this->method,
            'uri' => $this->uri,
            'params' => $this->params,
        ];
    }
}
