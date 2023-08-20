<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Contracts;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Throwable;

interface InterruptionHandlerContract
{
    public function report(Request $request, Throwable $interruption): void;

    public function render(Request $request, Response $response, Throwable $interruption): void;
}
