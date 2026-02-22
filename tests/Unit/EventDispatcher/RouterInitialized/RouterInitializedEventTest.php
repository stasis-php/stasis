<?php

namespace Stasis\Tests\Unit\EventDispatcher\RouterInitialized;

use PHPUnit\Framework\MockObject\Stub;
use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\RouterInitialized\ListenRouterInitializedInterface;
use Stasis\EventDispatcher\RouterInitialized\RouterInitializedData;
use Stasis\EventDispatcher\RouterInitialized\RouterInitializedEvent;
use Stasis\Router\Router;
use Stasis\Tests\Unit\EventDispatcher\Event\EventTestCase;

class RouterInitializedEventTest extends EventTestCase
{
    private Stub&Router $router;

    public function setUp(): void
    {
        parent::setUp();
        $this->router = $this->createStub(Router::class);
    }

    protected function getEvent(): EventInterface
    {
        return new RouterInitializedEvent($this->router);
    }

    protected function getEventData(): mixed
    {
        return new RouterInitializedData($this->router);
    }

    protected function getListenerClass(): string
    {
        return ListenRouterInitializedInterface::class;
    }

    protected function getListenerMethod(): string
    {
        return 'onRouterInitialized';
    }
}
