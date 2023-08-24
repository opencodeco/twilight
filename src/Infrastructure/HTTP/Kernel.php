<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP;

use Psr\Container\ContainerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Throwable;
use Twilight\Infrastructure\HTTP\Contracts\InterruptionHandlerContract;
use Twilight\Infrastructure\HTTP\Contracts\RouterContract;

readonly class Kernel
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public static function create(ContainerInterface $container): self
    {
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

            /** @var RouterContract $router */
            $router = $this->container->get(RouterContract::class);
            $router->handle($method, $uri);
        } catch (Throwable $interruption) {
            $handler = $this->container->get(InterruptionHandlerContract::class);
            /** @var InterruptionHandlerContract $handler */
            $handler->report($request, $interruption);
            $handler->render($request, $response, $interruption);
        }
    }
}
