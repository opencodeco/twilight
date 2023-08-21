<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Routing;

use JsonSerializable;
use Twilight\Infrastructure\HTTP\Contracts\RouteContract;

final readonly class Route implements JsonSerializable, RouteContract
{
    public function __construct(
        protected int $id,
        protected string $method,
        protected string $uri,
        protected array $params
    ) {
    }

    public static function make(int $id, string $method, string $uri, array $params): self
    {
        return new self($id, $method, $uri, $params);
    }

    public function id(): int
    {
        return $this->id;
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
            'id' => $this->id,
            'method' => $this->method,
            'uri' => $this->uri,
            'params' => $this->params,
        ];
    }
}
