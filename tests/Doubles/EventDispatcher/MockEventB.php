<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\EventDispatcher;

final class MockEventB extends AbstractMockEvent
{
    #[\Override]
    public function listenerClass(): string
    {
        return MockListenerB::class;
    }
}
