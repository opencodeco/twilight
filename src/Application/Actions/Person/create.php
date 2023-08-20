<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Ramsey\Uuid\Uuid;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\JSON;

return static function (Request $request, Response $response, CacheContract $cache) {
    $uuid = Uuid::uuid7();
    $data = JSON::from($request->getContent())->parse();
    $id = $uuid->toString();
    $data['id'] = $id;

    $response->status(201);
    $response->header('Location', "/pessoas/$id");
    $response->end(JSON::from($data)->stringify(true));

    $cache->set("person:$id", $data);
};
