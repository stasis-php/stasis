<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher\Event\RouteConfigure;

use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\Event\RouteConfigure\RouteConfigureData;
use Stasis\EventDispatcher\Event\RouteConfigure\RouteConfigureEvent;
use Stasis\EventDispatcher\Listener\RouteConfigureInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Source\RouteSourceCollection;

class RouteConfigureEventTest extends TestCase
{
    public function testAcceptMatchingListener(): void
    {
        $routes = new RouteSourceCollection();
        $event = new RouteConfigureEvent($routes);
        $data = new RouteConfigureData($routes);

        $listener = $this->createMock(RouteConfigureInterface::class);
        $listener
            ->expects($this->once())
            ->method('onRouteConfigure')
            ->with(self::equalTo($data));

        $isAccepted = $event->accept($listener);
        self::assertTrue($isAccepted, 'Event should be accepted by a matching listener.');
    }

    public function testAcceptNonMatchingListener(): void
    {
        $routes = new RouteSourceCollection();
        $event = new RouteConfigureEvent($routes);

        $listener = $this->createMock(ListenerInterface::class);
        $isAccepted = $event->accept($listener);
        self::assertFalse($isAccepted, 'Event should not be accepted by a non-matching listener.');
    }
}
