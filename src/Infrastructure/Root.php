<?php

declare(strict_types=1);

namespace Twilight\Infrastructure;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Throwable;

readonly class Root
{
    public function __construct(private Router $router)
    {
    }

    public static function create(): self
    {
        return new self(new Router());
    }

    public function __invoke(Request $request, Response $response): void
    {
        try {
            $method = strtolower($request->server['request_method'] ?? '');
            $uri = $request->server['request_uri'] ?? '';
            $this->router
                ->using($request, $response)
                ->handle($method, $uri);
        } catch (Throwable $error) {
            echo "'{$error->getMessage()}' at " .
                "'{$error->getFile()}' on {$error->getLine()}\n" .
                $error->getTraceAsString();
        }
    }
}
