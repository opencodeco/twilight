<?php

declare(strict_types=1);

namespace Twilight\Infrastructure\Persistence\Person;

use Twilight\Domain\Person\Person;
use Twilight\Domain\Person\PersonRepository;

class MySQLPersonRepository implements PersonRepository
{

    public function create(Person $person): Person
    {
        // TODO: Implement create() method.
    }

    public function count(): int
    {
        // TODO: Implement count() method.
    }
}
