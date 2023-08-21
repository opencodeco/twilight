<?php

declare(strict_types=1);

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

    dispatch($response, $id, $data);

    register($cache, $database, $logger, $id, $data);
};

function dispatch(Response $response, string $id, array $data): void
{
    $response->status(201);
    $response->header('Location', "/pessoas/$id");
    $response->end(JSON::from($data)->stringify(true));
}

function register(
    CacheContract $cache,
    DatabaseContract $database,
    LoggerInterface $logger,
    string $id,
    array $data
): void {
    $cache->set("person:$id", $data);

    ['apelido' => $nickname, 'nome' => $name, 'nascimento' => $birthdate, 'stack' => $stack] = $data;
    $stack = JSON::from($stack)->stringify();
    $searchable = strtolower($name) . '|' . strtolower($nickname) . '|' . strtolower($stack);
    $database->execute(
        'insert into people (id, nick, name, searchable, birth, stack) values (?, ?, ?, ?, ?, ?)',
        [$id, $nickname, $name, $searchable, $birthdate, $stack]
    );
    $logger->info("Person $id created", $data);
}
