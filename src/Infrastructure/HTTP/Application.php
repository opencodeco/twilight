<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\HTTP;

use DI\Container;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Predis\Client as Redis;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Throwable;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\Cache\RedisClient;
use Twilight\Infrastructure\Database\Contract\DatabaseContract;
use Twilight\Infrastructure\Database\MySQL;
use Twilight\Infrastructure\HTTP\Contracts\RouterContract;
use Twilight\Infrastructure\JSON;

readonly class Application
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public static function create(): self
    {
        $container = new Container();

        $router = Router::create($container);
        $routing = require __DIR__ . '/../../../config/routes.php';
        $routing($router);
        $container->set(RouterContract::class, $router);

        $container->set(CacheContract::class, function () {
            return RedisClient::create(new Redis([
                'scheme' => 'tcp',
                'host' => 'cache',
                'port' => 6379,
            ]));
        });

        $container->set(LoggerInterface::class, function () {
            // rotate log file on size
            $filename = __DIR__ . '/../../../server.log';
            if (is_file($filename) && filesize($filename) > 100000) {
                $pieces = pathinfo($filename);
                $pattern = $pieces['dirname'] . '/' . $pieces['filename'] . "-%d." . $pieces['extension'];
                $last = sprintf($pattern, 3);
                if (is_file($last)) {
                    unlink($last);
                }
                for ($i = 3 - 1; $i > 0; $i--) {
                    $file = sprintf($pattern, $i);
                    if (is_file($file)) {
                        rename($file, sprintf($pattern, $i + 1));
                    }
                }
                rename($filename, sprintf($pattern, 1));
            }

            $log = new Logger('app');
            $log->pushHandler(new StreamHandler($filename, Level::Debug));
            return $log;
        });

        return new self($container);
    }

    public function __invoke(Request $request, Response $response): void
    {
        try {
            $method = strtolower($request->server['request_method'] ?? '');
            $uri = $request->server['request_uri'] ?? '';

            $response->header('Content-Type', 'application/json');

            $this->container->set(Request::class, $request);
            $this->container->set(Response::class, $response);
            $this->container->set(DatabaseContract::class, function () {
                return MySQL::create('mysql:host=db;dbname=twilight', 'root', 'root');
            });

            $router = $this->container->get(RouterContract::class);

            $router->handle($method, $uri);
        } catch (Throwable $error) {
            $this->treat($response, $error);
        }
    }

    private function treat(Response $response, Throwable $error): void
    {
        $response->header('Content-Type', 'application/json');
        $response->status(500);
        $data = [
            'message' => $error->getMessage(),
            'file' => $error->getFile(),
            'line' => $error->getLine(),
            'trace' => $error->getTraceAsString(),
        ];
        $response->end(JSON::from($data)->stringify(true));
    }
}
