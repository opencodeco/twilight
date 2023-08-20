<?php

declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Predis\Client as Redis;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Twilight\Infrastructure\Cache\Contracts\CacheContract;
use Twilight\Infrastructure\Cache\RedisClient;
use Twilight\Infrastructure\Database\Contract\DatabaseContract;
use Twilight\Infrastructure\Database\MySQL;
use Twilight\Infrastructure\HTTP\Contracts\InterruptionHandlerContract;
use Twilight\Infrastructure\HTTP\Contracts\RouterContract;
use Twilight\Infrastructure\HTTP\Router;
use Twilight\Infrastructure\JSON;

function env(string $key, string|array|null|false|int|float $default = null): string|array|null|false|int|float
{
    return getenv($key) ?? $default;
}

return static function (ContainerInterface $container) {
    $router = Router::create($container);
    $routing = require __DIR__ . '/routes.php';
    $routing($router);
    $container->set(RouterContract::class, $router);

    $container->set(CacheContract::class, function () {
        return RedisClient::create(new Redis([
            'scheme' => 'tcp',
            'host' => env('CACHE_HOST', 'cache'),
            'port' => env('CACHE_PORT', 6379),
        ]));
    });

    $container->set(LoggerInterface::class, function () {
        $filename = __DIR__ . '/../server.log';
        if (is_file($filename) && filesize($filename) > (int)env('LOG_MAX_SIZE', 100000)) {
            $pieces = pathinfo($filename);
            $pattern = $pieces['dirname'] . '/' . $pieces['filename'] . "-%d." . $pieces['extension'];
            $maxFiles = (int)env('LOG_MAX_FILES', 3);
            $last = sprintf($pattern, $maxFiles);
            if (is_file($last)) {
                unlink($last);
            }
            for ($i = $maxFiles - 1; $i > 0; $i--) {
                $file = sprintf($pattern, $i);
                if (is_file($file)) {
                    rename($file, sprintf($pattern, $i + 1));
                }
            }
            rename($filename, sprintf($pattern, 1));
        }

        $log = new Logger('app');
        $level = Level::fromName((string)env('LOG_LEVEL', 'Debug'));
        $log->pushHandler(new StreamHandler($filename, $level));
        return $log;
    });

    $container->set(DatabaseContract::class, function () {
        return MySQL::create(
            env('DB_DSN', 'mysql:host=db;dbname=twilight'),
            env('DB_USER', 'root'),
            env('DB_PASSWORD', 'root')
        );
    });

    $container->set(InterruptionHandlerContract::class, function () use ($container) {
        return new class($container->get(LoggerInterface::class)) implements InterruptionHandlerContract {
            public function __construct(private readonly LoggerInterface $logger)
            {
            }

            public function report(Request $request, Throwable $interruption): void
            {
                $context = [
                    'message' => $interruption->getMessage(),
                    'file' => $interruption->getFile(),
                    'line' => $interruption->getLine(),
                    'trace' => $interruption->getTraceAsString(),
                ];
                $this->logger->error($interruption->getMessage(), $context);
            }

            public function render(
                Request $request,
                Response $response,
                Throwable $interruption
            ): void {
                $response->header('Content-Type', 'application/json');
                $response->status(500);
                $context = [
                    'message' => $interruption->getMessage(),
                    'file' => $interruption->getFile(),
                    'line' => $interruption->getLine(),
                    'trace' => $interruption->getTraceAsString(),
                ];
                $response->end(JSON::from($context)->stringify());
            }
        };
    });
};
