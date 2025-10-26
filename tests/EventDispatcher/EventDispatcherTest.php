<?php

declare(strict_types=1);

namespace Stasis\Tests\EventDispatcher;

use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\Tests\EventDispatcher\Test\TestEventA;
use Stasis\Tests\EventDispatcher\Test\TestListenerA;
use Stasis\Tests\EventDispatcher\Test\TestListenerB;

class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;

    public function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testDispatchCachesAndSkipsNonMatchingOnSecondRun(): void
    {
        $listenerA1 = new TestListenerA();
        $listenerA2 = new TestListenerA();
        $listenerB1 = new TestListenerB();

        $this->dispatcher->add($listenerA1);
        $this->dispatcher->add($listenerB1);
        $this->dispatcher->add($listenerA2);

        $eventA1 = new TestEventA();
        $this->dispatcher->dispatch($eventA1);

        self::assertSame(
            [
                TestListenerA::class,
                TestListenerB::class,
                TestListenerA::class,
            ],
            $eventA1->acceptedWith,
            'Unexpected accepted listeners of event1.',
        );
        self::assertSame(1, $listenerA1->handleCount, 'Unexpected handle count of listener A1.');
        self::assertSame(1, $listenerA2->handleCount, 'Unexpected handle count of listener A2.');

        $eventA2 = new TestEventA();
        $this->dispatcher->dispatch($eventA2);

        // event of the same type trigger take only matching (cached) listeners
        self::assertSame(
            [
                TestListenerA::class,
                TestListenerA::class,
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
        $listenerA1 = new TestListenerA();
        $listenerB1 = new TestListenerB();

        $this->dispatcher->add($listenerA1);
        $this->dispatcher->add($listenerB1);

        $eventA1 = new TestEventA();
        $this->dispatcher->dispatch($eventA1);

        $listenerA2 = new TestListenerA();
        $this->dispatcher->add($listenerA2);

        $eventA2 = new TestEventA();
        $this->dispatcher->dispatch($eventA2);

        self::assertSame(
            [
                TestListenerA::class,
                TestListenerB::class,
                TestListenerA::class,
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
        $listenerA1 = new TestListenerA();
        $listenerA2 = new TestListenerA();
        $listenerB1 = new TestListenerB();

        $this->dispatcher->add($listenerA1);
        $this->dispatcher->add($listenerB1);
        $this->dispatcher->add($listenerA2);

        $eventA1 = new TestEventA();
        $this->dispatcher->dispatch($eventA1);

        $this->dispatcher->remove($listenerA2);

        $eventA2 = new TestEventA();
        $this->dispatcher->dispatch($eventA2);

        self::assertSame(
            [
                TestListenerA::class,
                TestListenerB::class,
            ],
            $eventA2->acceptedWith,
            'Unexpected accepted listeners of event2.',
        );
        self::assertSame(2, $listenerA1->handleCount, 'Unexpected handle count of listener A1.');
        self::assertSame(1, $listenerA2->handleCount, 'Unexpected handle count of listener A2.');
        self::assertSame(0, $listenerB1->handleCount, 'Unexpected handle count of listener B1.');
    }
}
