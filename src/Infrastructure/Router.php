<?php

declare(strict_types=1);

namespace Twilight\Infrastructure;

use Swoole\Http\Request;
use Swoole\Http\Response;

class Router
{
    private Request $request;

    private Response $response;

    public function __construct()
    {
    }

    public function using(Request $request, Response $response): static
    {
        $this->request = $request;
        $this->response = $response;
        return $this;
    }

    public function handle(string $method, string $uri): void
    {
        $method = strtolower($method);
        $uri = strtolower($uri);
        $endpoint = "$method:$uri";
        match ($endpoint) {
            'get:/' => $this->home(),
            default => $this->notFound($method, $uri),
        };
        $this->response->end("$method:$uri");
    }
}
