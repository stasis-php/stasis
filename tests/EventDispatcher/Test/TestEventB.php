<?php

declare(strict_types=1);

namespace Stasis\Tests\EventDispatcher\Test;

final class TestEventB extends AbstractTestEvent
{
    public function listenerClass(): string
    {
        return TestListenerB::class;
    }
}
