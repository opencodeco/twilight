<?php

use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\Database\Contract\DatabaseContract;
use Twilight\Infrastructure\JSON;

return static function (
    Request $request,
    Response $response,
    CacheContract $cache,
    DatabaseContract $database,
    LoggerInterface $logger
) {
    $logger->info('Creating person');

    $uuid = Uuid::uuid7();
    $data = (array)JSON::from($request->getContent())->parse();
    $id = $uuid->toString();
    $data['id'] = $id;

    dispatch($response, $id, $data, $cache);

    register($id, $data, $database, $logger);
};

function dispatch(Response $response, string $id, array $data, CacheContract $cache): void
{
    $response->status(201);
    $response->header('Location', "/pessoas/$id");
    $response->end(JSON::from($data)->stringify(true));

    $cache->set("person:$id", $data);
}

function register($id, $data, $database, $logger): void
{
    ['apelido' => $nickname, 'nome' => $name, 'nascimento' => $birthdate, 'stack' => $stack] = $data;
    $stack = JSON::from($stack)->stringify();
    $searchable = strtolower($name) . '|' . strtolower($nickname) . '|' . strtolower($stack);
    $database->execute(
        'insert into people (id, nick, name, searchable, birth, stack) values (?, ?, ?, ?, ?, ?)',
        [$id, $nickname, $name, $searchable, $birthdate, $stack]
    );
    $logger->info("Person $id created", $data);
}
