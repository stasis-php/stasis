<?php

namespace Stasis\Tests\Unit\EventDispatcher\RouterReady;

use PHPUnit\Framework\MockObject\Stub;
use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\RouterReady\RouterReadyListenerInterface;
use Stasis\EventDispatcher\RouterReady\RouterReadyData;
use Stasis\EventDispatcher\RouterReady\RouterReadyEvent;
use Stasis\Router\Router;
use Stasis\Tests\Unit\EventDispatcher\EventTestCase;

class RouterReadyEventTest extends EventTestCase
{
    private Stub&Router $router;
    private \Generator $routes;

    public function setUp(): void
    {
        parent::setUp();
        $this->router = $this->createStub(Router::class);
        $this->routes = (static fn() => yield from [])();
    }

    protected function getEvent(): EventInterface
    {
        return new RouterReadyEvent($this->router, $this->routes);
    }

    protected function getEventData(): mixed
    {
        return new RouterReadyData($this->router, $this->routes);
    }

    protected function getListenerClass(): string
    {
        return RouterReadyListenerInterface::class;
    }

    protected function getListenerMethod(): string
    {
        return 'onRouterReady'; /* @see RouterReadyListenerInterface::onRouterReady */
    }
}
