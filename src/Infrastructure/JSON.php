<?php

declare(strict_types=1);

namespace Twilight\Infrastructure;

use JsonException;
use Throwable;

class JSON
{
    private static Throwable $error;

    public static function stringify(mixed $value, bool $pretty = false): ?string
    {
        try {
            if ($pretty) {
                return json_encode($value, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_IGNORE | JSON_PRETTY_PRINT);
            }
            return json_encode($value, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_IGNORE);
        } catch (JsonException $e) {
            static::$error = $e;
            return null;
        }
    }

    public static function parse(string $value, bool $associative = true): mixed
    {
        try {
            return json_decode($value, $associative, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            static::$error = $e;
            return null;
        }
    }

    public static function error(): Throwable
    {
        return static::$error;
    }
}
