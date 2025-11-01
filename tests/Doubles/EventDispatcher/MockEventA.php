<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\EventDispatcher;

final class MockEventA extends AbstractMockEvent
{
    public function listenerClass(): string
    {
        return MockListenerA::class;
    }
}
