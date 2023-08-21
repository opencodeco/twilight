#!/usr/bin/env php
<?php

declare(strict_types=1);

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

use Swoole\Http\Server;
use Twilight\Infrastructure\HTTP\Kernel;

$http = new Server('0.0.0.0', 9501);

$http->on('start', static function () {
    echo "Twilight is started at http://0.0.0.0:9501\n";
});

$http->on('request', Kernel::create());

$http->start();
