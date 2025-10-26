<?php

declare(strict_types=1);

namespace Stasis\Tests\EventDispatcher\Test;

use Stasis\EventDispatcher\ListenerInterface;

abstract class AbstractTestListener implements ListenerInterface
{
    public private(set) int $handleCount = 0;

    public function onTestEvent(): void
    {
        $this->handleCount++;
    }
}
