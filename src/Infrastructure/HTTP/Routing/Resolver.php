<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP\Routing;

use Throwable;
use Twilight\Infrastructure\HTTP\Contracts\ResolverContract;

final class Resolver implements ResolverContract
{
    private string $pattern = '!';

    public function append(string $method, string $uri, int $id): self
    {
        $method = strtoupper($method);
        $uri = $this->normalizeURI($uri);
        $endpoint = "$method:$uri";
        $this->pattern .= "|^(?<routing_$id>#):$endpoint\/?\$";
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

    public function pattern(): string
    {
        return $this->pattern;
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

    private function normalizeURI(string $uri): string
    {
        if ($uri === '/') {
            return '';
        }
        $uri = strtolower(trim($uri, '/'));
        $uri = '/' . $uri;
        $uri = preg_replace('/(\/+)/', '/', $uri);
        $uri = str_replace('/', '\/', $uri);
        return $this->configureRegexURI($uri);
    }

    private function configureRegexURI(string $uri): string
    {
        $callback = static function ($matches) {
            try {
                $match = $matches[1] ?? null;
                if (!is_string($match)) {
                    return '([^\/]*)';
                }
                $pieces = explode('|', $match);
                $name = trim(array_shift($pieces));
                if (!count($pieces)) {
                    return "(?<$name>[^\/]*)";
                }
                $config = trim(array_shift($pieces));
                $matcher = match ($config) {
                    'uuid' => '[^\/]\w{8}-\w{4}-\w{4}-\w{4}-\w{12}',
                    'int' => '[^\/]\d+',
                    default => "[^\/]*"
                };
                return "(?<$name>$matcher)";
            } catch (Throwable) {
                return '([^\/]*)';
            }
        };
        return preg_replace_callback('/\{[\s+]?([^}]+)[\s+]?}/', $callback, $uri);
    }
}
