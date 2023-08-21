<?php

declare(strict_types=1);

use Swoole\Http\Response;
use Twilight\Infrastructure\HTTP\Routing\Router;
use Twilight\Infrastructure\JSON;

return static function (Router $router) {
    $router->get('/', require __DIR__ . '/../src/Application/Actions/home.php');

    $router->post('/pessoas', require __DIR__ . '/../src/Application/Actions/Person/create.php');
    $router->get('/pessoas/(?<id>[\w-]+)', require __DIR__ . '/../src/Application/Actions/Person/read.php');
    $router->get('/pessoas', require __DIR__ . '/../src/Application/Actions/Person/find.php');
    $router->get('/contagem-pessoas', require __DIR__ . '/../src/Application/Actions/Person/count.php');

    $router->all('(.*)', function (Response $response) {
        $response->status(404);
        $response->end(JSON::from(['error' => 'no route to resource'])->stringify());
    });
};
