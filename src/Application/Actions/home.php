<?php

declare(strict_types=1);

use Twilight\Infrastructure\Event\Contracts\EventManagerContract;

return static function (EventManagerContract $eventManager) {
    $eventManager->dispatch('inspire', 'Twilight');
    return 'Here is Twilight!';
};
