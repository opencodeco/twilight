<?php

declare(strict_types=1);

namespace Twilight\Domain\Person;

interface PersonRepository
{
    public function create(Person $person): Person;

    public function count(): int;
}
