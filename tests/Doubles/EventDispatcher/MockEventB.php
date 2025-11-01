<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\EventDispatcher;

final class MockEventB extends AbstractMockEvent
{
    public function listenerClass(): string
    {
        return MockListenerB::class;
    }
}
