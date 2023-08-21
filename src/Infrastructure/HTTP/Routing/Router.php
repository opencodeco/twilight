<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Routing;

use Closure;
use Psr\Container\ContainerInterface;
use Swoole\Http\Response;
use Twilight\Infrastructure\HTTP\Contracts\ResolverContract;
use Twilight\Infrastructure\HTTP\Contracts\RouteContract;
use Twilight\Infrastructure\HTTP\Contracts\RouterContract;
use Twilight\Infrastructure\JSON;

final class Router implements RouterContract
{
    private array $candidates = [];

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly ResolverContract $resolver
    ) {
    }

    public static function create(ContainerInterface $container, ResolverContract $resolver = null): self
    {
        return new self($container, $resolver ?? new Resolver());
    }

    public function add(string $method, string $uri, Closure $handler): static
    {
        $id = count($this->candidates);
        $this->resolver->append($method, $uri, $id);
        $this->candidates[] = $handler;
        return $this;
    }

    public function get(string $uri, Closure $handler): static
    {
        return $this->add('get', $uri, $handler);
    }

    public function post(string $uri, Closure $handler): static
    {
        return $this->add('post', $uri, $handler);
    }

    public function put(string $uri, Closure $handler): static
    {
        return $this->add('put', $uri, $handler);
    }

    public function patch(string $uri, Closure $handler): static
    {
        return $this->add('patch', $uri, $handler);
    }

    public function delete(string $uri, Closure $handler): static
    {
        return $this->add('delete', $uri, $handler);
    }

    public function all(string $uri, Closure $handler): static
    {
        foreach (['get', 'post', 'put', 'patch', 'delete'] as $item) {
            $this->add($item, $uri, $handler);
        }
        return $this;
    }

    public function __call(string $name, array $arguments)
    {
        $this->add($name, ...$arguments);
    }

    public function handle(string $method, string $uri): void
    {
        $route = $this->resolver->match($method, $uri);
        $index = $route?->id();
        $candidate = $this->candidates[$index] ?? null;

        $response = $this->container->get(Response::class);
        if (!$candidate) {
            return;
        }

        $this->container->set(RouteContract::class, $route);
        $content = $this->container->call($candidate);
        if ($response->isWritable()) {
            $response->end(JSON::from($content)->stringify());
        }
    }
}
