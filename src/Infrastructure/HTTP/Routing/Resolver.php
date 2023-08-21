<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Routing;

use Twilight\Infrastructure\HTTP\Contracts\ResolverContract;

final class Resolver implements ResolverContract
{
    private string $pattern = '!';

    public function append(string $method, string $uri, int $id): self
    {
        $method = strtoupper($method);
        $uri = str_replace('/', '\/', strtolower($uri));
        $endpoint = "$method:$uri";
        $this->pattern .= "|^(?<routing_$id>#):$endpoint\$";
        return $this;
    }

    public function match(string $method, string $uri): ?Route
    {
        $method = strtoupper($method);
        $uri = strtolower($uri);
        $endpoint = "#:$method:$uri";
        $pattern = "/$this->pattern/";
        preg_match_all($pattern, $endpoint, $matches, PREG_SET_ORDER);
        $params = $matches[0] ?? [];
        $id = $this->extractId($params);
        if (!$id) {
            return null;
        }
        return Route::make($id, $method, $uri, $params);
    }

    private function extractId(array $matches): ?int
    {
        foreach ($matches as $key => $value) {
            if (!$value || !is_string($key)) {
                continue;
            }
            if (str_starts_with($key, 'routing_')) {
                return (int)str_replace('routing_', '', $key);
            }
        }
        return null;
    }
}
