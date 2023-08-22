<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\Cache\Contracts;

interface CacheContract
{
    public function set(string $key, mixed $value, int $ttl = 3600): bool;

    public function get(string $key): mixed;

    public function append(string $key, string $value): bool;

    public function delete(string $key): bool;
}
