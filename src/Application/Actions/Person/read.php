<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\HTTP\Route;
use Twilight\Infrastructure\JSON;

return static function (Request $request, Response $response, CacheContract $cache, Route $route) {
    $id = $route->param('id');
    $data = $cache->get("person:$id");
    if ($data === null) {
        $response->status(404);
        $response->end('not found');
        return;
    }
    $response->end(JSON::from($data)->stringify());
};
