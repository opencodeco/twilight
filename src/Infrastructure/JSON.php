<?php

declare(strict_types=1);

namespace Twilight\Infrastructure;

use JsonException;

class JSON
{
    private array $errors;

    public function __construct(private readonly mixed $value)
    {
    }

    public static function from(mixed $value): self
    {
        return new self($value);
    }

    public function stringify(bool $pretty = false): ?string
    {
        try {
            if ($pretty) {
                return json_encode($this->value, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_IGNORE | JSON_PRETTY_PRINT);
            }
            return json_encode($this->value, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_IGNORE);
        } catch (JsonException $e) {
            $this->errors[] = $e;
            return null;
        }
    }

    public function parse(): mixed
    {
        try {
            return json_decode($this->value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            $this->errors[] = $e;
            return null;
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
