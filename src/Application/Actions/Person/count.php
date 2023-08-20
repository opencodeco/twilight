<?php

use Swoole\Http\Response;
use Twilight\Infrastructure\Database\Contract\DatabaseContract;

return static function (DatabaseContract $database, Response $response) {
    $response->status(200);
    $statement = $database->execute('select count(id) as value from people');
    $statement->setFetchMode(PDO::FETCH_OBJ);
    $count = $statement->fetch();
    $response->end($count->value);
};

