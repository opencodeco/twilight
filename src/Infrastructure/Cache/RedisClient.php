<?php

namespace Twilight\Infrastructure\Cache;

use Predis\ClientInterface;
use Throwable;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\JSON;

readonly class RedisClient implements CacheContract
{
    public function __construct(protected ClientInterface $client)
    {
    }

    public static function create(ClientInterface $client): self
    {
        return new self($client);
    }

    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        try {
            $this->client->set($key, JSON::stringify($value));
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function get(string $key): mixed
    {
        try {
            $value = $this->client->get($key);
        } catch (Throwable) {
            return null;
        }
        if ($value === null) {
            return null;
        }
        return JSON::parse($value);
    }

    public function append(string $key, string $value): bool
    {
        try {
            $this->set($key, $this->get($key) . $value);
            return true;
        } catch (Throwable) {
            return false;
        }
    }

    public function delete(string $key): bool
    {
        try {
            $this->client->del($key);
            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
