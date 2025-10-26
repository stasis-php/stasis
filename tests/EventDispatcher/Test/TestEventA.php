<?php

declare(strict_types=1);

namespace Stasis\Tests\EventDispatcher\Test;

final class TestEventA extends AbstractTestEvent
{
    public function listenerClass(): string
    {
        return TestListenerA::class;
    }
}
