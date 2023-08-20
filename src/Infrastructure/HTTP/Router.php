<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP;

use Psr\Container\ContainerInterface;
use Swoole\Http\Response;
use Twilight\Infrastructure\HTTP\Contracts\RouterContract;
use Twilight\Infrastructure\JSON;

/**
 * @method Router get(string $uri, callable $handler)
 * @method Router post(string $uri, callable $handler)
 * @method Router put(string $uri, callable $handler)
 * @method Router patch(string $uri, callable $handler)
 * @method Router delete(string $uri, callable $handler)
 */
class Router implements RouterContract
{
    private array $candidates = [];

    private string $pattern = '!';

    public function __construct(private readonly ContainerInterface $container)
    {
    }

    public static function create(ContainerInterface $container): self
    {
        return new self($container);
    }

    public function add(string $method, string $uri, callable $handler): static
    {
        $method = strtoupper($method);
        $uri = str_replace('/', '\/', strtolower($uri));
        $endpoint = "$method:$uri";
        $index = count($this->candidates);
        $this->pattern .= "|^(?<routing_$index>#):$endpoint\$";
        $this->candidates[] = $handler;
        return $this;
    }

    public function __call(string $name, array $arguments)
    {
        $this->add($name, ...$arguments);
    }

    public function handle(string $method, string $uri): void
    {
        $method = strtoupper($method);
        $uri = strtolower($uri);
        $endpoint = "#:$method:$uri";

        preg_match_all("/$this->pattern/", $endpoint, $matches, PREG_SET_ORDER, 0);
        $params = $matches[0] ?? [];
        $this->container->set(Route::class, Route::make($method, $uri, $params));

        $index = $this->extract($params);
        $candidate = $this->candidates[$index] ?? null;

        $response = $this->container->get(Response::class);
        if (!$candidate) {
            $response->status(404);
            $response->end(JSON::from([
                'error' => 'not found',
                'pattern' => "/$this->pattern/",
                'endpoint' => $endpoint,
            ])->stringify());
            return;
        }
        $content = $this->container->call($candidate);
        if ($response->isWritable()) {
            $response->end(JSON::from($content)->stringify());
        }
    }

    private function extract(array $matches): ?int
    {
        foreach ($matches as $key => $value) {
            if (!$value || !is_string($key)) {
                continue;
            }
            if (str_starts_with($key, 'routing_')) {
                return (int)str_replace('routing_', '', $key);
            }
        }
        return null;
    }
}
