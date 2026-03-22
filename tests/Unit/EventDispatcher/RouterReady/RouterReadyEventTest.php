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

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->router = self::createStub(Router::class);
        $this->routes = (static fn() => yield from [])();
    }

    #[\Override]
    protected function getEvent(): EventInterface
    {
        return new RouterReadyEvent($this->router, $this->routes);
    }

    #[\Override]
    protected function getEventData(): mixed
    {
        return new RouterReadyData($this->router, $this->routes);
    }

    #[\Override]
    protected function getListenerClass(): string
    {
        return RouterReadyListenerInterface::class;
    }

    #[\Override]
    protected function getListenerMethod(): string
    {
        return 'onRouterReady'; /* @see RouterReadyListenerInterface::onRouterReady */
    }
}
