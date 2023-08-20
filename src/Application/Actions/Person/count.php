<?php

use Swoole\Http\Request;
use Swoole\Http\Response;

return static function (Request $request, Response $response) {
    $response->end('count');
};
