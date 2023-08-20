#!/usr/bin/env php
<?php

declare(strict_types=1);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    echo "autoload was required";
}

use Swoole\Http\Server;
use Twilight\Infrastructure\Root;

$http = new Server('0.0.0.0', 9501);

$http->on('start', static function () {
    echo "http server is started at http://0.0.0.0:9501\n";
});

$http->on('request', Root::create());

$http->start();
