<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher\Event;

use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\ListenerInterface;

abstract class EventTestCase extends TestCase
{
    abstract protected function getEvent(): EventInterface;

    abstract protected function getEventData(): mixed;

    /** @return class-string<ListenerInterface> */
    abstract protected function getListenerClass(): string;

    /** @return non-empty-string */
    abstract protected function getListenerMethod(): string;

    public function testAcceptMatchingListener(): void
    {
        $event = $this->getEvent();
        $data = $this->getEventData();
        $listener = $this->createMock($this->getListenerClass());
        $method = $this->getListenerMethod();

        $listener
            ->expects($this->once())
            ->method($method)
            ->with(self::equalTo($data));

        $isAccepted = $event->accept($listener);
        self::assertTrue($isAccepted, 'Event should be accepted by a matching listener.');
    }

    public function testAcceptNonMatchingListener(): void
    {
        $event = $this->getEvent();
        $listener = $this->createMock(ListenerInterface::class);
        $isAccepted = $event->accept($listener);
        self::assertFalse($isAccepted, 'Event should not be accepted by a non-matching listener.');
    }
}
