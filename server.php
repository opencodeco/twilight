#!/usr/bin/env php
<?php

declare(strict_types=1);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

use DI\Container;
use Swoole\Http\Server;
use Twilight\Infrastructure\HTTP\Kernel;

$server = new Server('0.0.0.0', 9501);
$container = new Container();
$configure = require __DIR__ . '/config/container.php';
$configure($container);
$kernel = Kernel::create($container);

$server->set(array(
    'worker_num' => 1,
    'task_worker_num' => 5,
));

$server->on('start', static function () {
    echo "Twilight is started at http://0.0.0.0:9501\n";
});

$server->on('request', $kernel);

$server->on('task', function (Server $server, $taskId, $workerId, $data) {
    $server->finish($data);
});

$server->on('finish', function ($server, $taskId, $data) {
    echo "Task $taskId finished:", json_encode($data, JSON_THROW_ON_ERROR), "\n";
});

$server->start();
