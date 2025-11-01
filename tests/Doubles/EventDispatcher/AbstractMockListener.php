<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\EventDispatcher;

use Stasis\EventDispatcher\ListenerInterface;

abstract class AbstractMockListener implements ListenerInterface
{
    public private(set) int $handleCount = 0;

    public function onTestEvent(): void
    {
        $this->handleCount++;
    }
}
