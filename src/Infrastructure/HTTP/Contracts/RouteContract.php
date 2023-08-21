<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Contracts;

interface RouteContract
{
    public function id(): int;

    public function method(): string;

    public function uri(): string;

    public function param(string $name): mixed;
}
