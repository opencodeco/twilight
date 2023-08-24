<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\Event;

use Psr\Container\ContainerInterface;
use RuntimeException;
use Twilight\Infrastructure\Event\Contracts\EventManagerContract;

final class EventManager implements EventManagerContract
{
    private array $events = [];

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public function listen(string $event, callable $callback): void
    {
        $this->events[$event][] = $callback;
    }

    public function dispatch(string $event, float|object|bool|int|string $payload): void
    {
        $callback = $this->events[$event] ?? null;
        if (!is_callable($callback)) {
            throw new RuntimeException('eventManager::dispatch: event not found');
        }
        $this->container->call($callback, ['payload' => $payload]);
    }
}
