<?php

use Swoole\Http\Request;
use Swoole\Http\Response;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\Database\Contract\DatabaseContract;
use Twilight\Infrastructure\JSON;

function extractKeys(array $matches): array
{
    $keys = [];
    foreach ($matches as $key => $value) {
        if (empty($value) || !is_string($key) || !is_array($value) || count($value) === 0 || empty($value[0])) {
            continue;
        }
        if (str_starts_with($key, 'routing_')) {
            $keys[] = str_replace('routing_', '', $key);
        }
    }
    return $keys;
}

return static function (Request $request, Response $response, CacheContract $cache, DatabaseContract $database) {
    $searchable = $request->get['t'] ?? null;
    if (!$searchable) {
        $response->status(400);
        $response->end(JSON::stringify(['error' => 'where is t?']));
        return;
    }
    $data = $database->execute('select * from people where searchable = ? limit 50', ["%$searchable%"])->fetchAll();
    if ($data) {
        $response->end(JSON::stringify($data));
        return;
    }
    $response->end('[]');
};
