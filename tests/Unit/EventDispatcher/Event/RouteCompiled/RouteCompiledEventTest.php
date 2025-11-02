<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher\Event\RouteCompiled;

use PHPUnit\Framework\TestCase;
use Stasis\EventDispatcher\Event\RouteCompiled\RouteCompiledData;
use Stasis\EventDispatcher\Event\RouteCompiled\RouteCompiledEvent;
use Stasis\EventDispatcher\Listener\RouteCompiledInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\Resource\ResourceInterface;

class RouteCompiledEventTest extends TestCase
{
    public function testAcceptMatchingListener(): void
    {
        $route = new CompiledRoute(
            '/page',
            '/page.html',
            $this->createMock(ResourceInterface::class),
        );

        $event = new RouteCompiledEvent($route);
        $data = new RouteCompiledData($route);

        $listener = $this->createMock(RouteCompiledInterface::class);
        $listener
            ->expects($this->once())
            ->method('onRouteCompiled')
            ->with(self::equalTo($data));

        $isAccepted = $event->accept($listener);
        self::assertTrue($isAccepted, 'Event should be accepted by a matching listener.');
    }

    public function testAcceptNonMatchingListener(): void
    {
        $route = new CompiledRoute(
            '/page',
            '/page.html',
            $this->createMock(ResourceInterface::class),
        );

        $event = new RouteCompiledEvent($route);

        $listener = $this->createMock(ListenerInterface::class);
        $isAccepted = $event->accept($listener);
        self::assertFalse($isAccepted, 'Event should not be accepted by a non-matching listener.');
    }
}
