<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher;

use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\Tests\Doubles\EventDispatcher\MockEventA;
use Stasis\Tests\Doubles\EventDispatcher\MockListenerA;
use Stasis\Tests\Doubles\EventDispatcher\MockListenerB;

class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;

    public function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testDispatchCachesAndSkipsNonMatchingOnSecondRun(): void
    {
        $listenerA1 = new MockListenerA();
        $listenerA2 = new MockListenerA();
        $listenerB1 = new MockListenerB();

        $this->dispatcher->add($listenerA1);
        $this->dispatcher->add($listenerB1);
        $this->dispatcher->add($listenerA2);

        $eventA1 = new MockEventA();
        $this->dispatcher->dispatch($eventA1);

        self::assertSame(
            [
                MockListenerA::class,
                MockListenerB::class,
                MockListenerA::class,
            ],
            $eventA1->acceptedWith,
            'Unexpected accepted listeners of event1.',
        );
        self::assertSame(1, $listenerA1->handleCount, 'Unexpected handle count of listener A1.');
        self::assertSame(1, $listenerA2->handleCount, 'Unexpected handle count of listener A2.');

        $eventA2 = new MockEventA();
        $this->dispatcher->dispatch($eventA2);

        // event of the same type trigger take only matching (cached) listeners
        self::assertSame(
            [
                MockListenerA::class,
                MockListenerA::class,
            ],
            $eventA2->acceptedWith,
            'Unexpected accepted listeners of event2.',
        );
        self::assertSame(2, $listenerA1->handleCount, 'Unexpected handle count of listener A1.');
        self::assertSame(2, $listenerA2->handleCount, 'Unexpected handle count of listener A2.');

        self::assertSame(0, $listenerB1->handleCount, 'Listener B should not be triggered.');
    }

    public function testAddClearsCache(): void
    {
        $listenerA1 = new MockListenerA();
        $listenerB1 = new MockListenerB();

        $this->dispatcher->add($listenerA1);
        $this->dispatcher->add($listenerB1);

        $eventA1 = new MockEventA();
        $this->dispatcher->dispatch($eventA1);

        $listenerA2 = new MockListenerA();
        $this->dispatcher->add($listenerA2);

        $eventA2 = new MockEventA();
        $this->dispatcher->dispatch($eventA2);

        self::assertSame(
            [
                MockListenerA::class,
                MockListenerB::class,
                MockListenerA::class,
            ],
            $eventA2->acceptedWith,
            'Unexpected accepted listeners of event2.',
        );
        self::assertSame(2, $listenerA1->handleCount, 'Unexpected handle count of listener A1.');
        self::assertSame(1, $listenerA2->handleCount, 'Unexpected handle count of listener A2.');
        self::assertSame(0, $listenerB1->handleCount, 'Unexpected handle count of listener B1.');
    }

    public function testRemoveClearsCache(): void
    {
        $listenerA1 = new MockListenerA();
        $listenerA2 = new MockListenerA();
        $listenerB1 = new MockListenerB();

        $this->dispatcher->add($listenerA1);
        $this->dispatcher->add($listenerB1);
        $this->dispatcher->add($listenerA2);

        $eventA1 = new MockEventA();
        $this->dispatcher->dispatch($eventA1);

        $this->dispatcher->remove($listenerA2);

        $eventA2 = new MockEventA();
        $this->dispatcher->dispatch($eventA2);

        self::assertSame(
            [
                MockListenerA::class,
                MockListenerB::class,
            ],
            $eventA2->acceptedWith,
            'Unexpected accepted listeners of event2.',
        );
        self::assertSame(2, $listenerA1->handleCount, 'Unexpected handle count of listener A1.');
        self::assertSame(1, $listenerA2->handleCount, 'Unexpected handle count of listener A2.');
        self::assertSame(0, $listenerB1->handleCount, 'Unexpected handle count of listener B1.');
    }
}
