<?php

declare(strict_types=1);

use Twilight\Infrastructure\HTTP\Router;

return static function (Router $router) {
    $router->get('/', require __DIR__ . '/../src/Application/Actions/home.php');

    $router->post('/pessoas', require __DIR__ . '/../src/Application/Actions/Person/create.php');
    $router->get('/pessoas/(?<id>[\w-]+)', require __DIR__ . '/../src/Application/Actions/Person/read.php');
    $router->get('/pessoas', require __DIR__ . '/../src/Application/Actions/Person/find.php');
    $router->get('/contagem-pessoas', require __DIR__ . '/../src/Application/Actions/Person/count.php');
};
