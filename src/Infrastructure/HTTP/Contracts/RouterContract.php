<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Contracts;

use Closure;

interface RouterContract
{
    public function add(string $method, string $uri, Closure $handler): static;

    public function get(string $uri, Closure $handler): static;

    public function post(string $uri, Closure $handler): static;

    public function put(string $uri, Closure $handler): static;

    public function patch(string $uri, Closure $handler): static;

    public function delete(string $uri, Closure $handler): static;

    public function all(string $uri, Closure $handler): static;

    public function __call(string $name, array $arguments);

    public function handle(string $method, string $uri): void;
}
