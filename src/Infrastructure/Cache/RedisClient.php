<?php

namespace Twilight\Infrastructure\Cache;

use Predis\ClientInterface;
use Predis\Response\Status;
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

    public function set(string $key, mixed $value, int $ttl = 3600): Status
    {
        return $this->client->set($key, JSON::from($value)->stringify(), 'EX', $ttl);
    }

    public function get(string $key): mixed
    {
        $value = $this->client->get($key);
        if ($value === null) {
            return null;
        }
        return JSON::from($value)->parse();
    }
}
