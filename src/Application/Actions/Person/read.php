<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\Database\Contract\DatabaseContract;
use Twilight\Infrastructure\HTTP\Contracts\RouteContract;
use Twilight\Infrastructure\JSON;

return static function (
    Request $request,
    Response $response,
    CacheContract $cache,
    RouteContract $route,
    DatabaseContract $database
) {
    $id = $route->param('id');
    $data = $cache->get("person:$id");
    if ($data) {
        $response->end($data);
        return;
    }
    $data = $database->execute('select * from people where id = ?', [$id])->fetch();
    if ($data) {
        $response->end(JSON::stringify($data));
        return;
    }
    $response->status(404);
    $response->end('not found');
};
