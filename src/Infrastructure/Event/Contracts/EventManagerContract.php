<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\Event\Contracts;

interface EventManagerContract
{
    public function listen(string $event, callable $callback): void;

    public function dispatch(string $event, int|float|bool|object|string $payload): void;
}
