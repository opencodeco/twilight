<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP;

use DI\Container;
use Predis\Client as Redis;
use Psr\Container\ContainerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Throwable;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\Cache\RedisClient;
use Twilight\Infrastructure\HTTP\Contracts\RouterContract;
use Twilight\Infrastructure\JSON;

readonly class Application
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public static function create(): self
    {
        $container = new Container();

        $router = Router::create($container);
        $routing = require __DIR__ . '/../../../config/routes.php';
        $routing($router);
        $container->set(RouterContract::class, $router);

        $cache = RedisClient::create(new Redis([
            'scheme' => 'tcp',
            'host' => 'cache',
            'port' => 6379,
        ]));
        $container->set(CacheContract::class, $cache);

        return new self($container);
    }

    public function __invoke(Request $request, Response $response): void
    {
        try {
            $method = strtolower($request->server['request_method'] ?? '');
            $uri = $request->server['request_uri'] ?? '';

            $response->header('Content-Type', 'application/json');

            $this->container->set(Request::class, $request);
            $this->container->set(Response::class, $response);

            $router = $this->container->get(RouterContract::class);

            $router->handle($method, $uri);
        } catch (Throwable $error) {
            $this->treat($response, $error);
        }
    }

    private function treat(Response $response, Throwable $error): void
    {
        $response->header('Content-Type', 'application/json');
        $response->status(500);
        $data = [
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
        ];
        $response->end(JSON::from($data)->stringify(true));
    }
}
