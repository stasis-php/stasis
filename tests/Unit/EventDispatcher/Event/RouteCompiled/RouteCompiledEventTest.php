<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher\Event\RouteCompiled;

use Stasis\EventDispatcher\Event\RouteCompiled\RouteCompiledData;
use Stasis\EventDispatcher\Event\RouteCompiled\RouteCompiledEvent;
use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\Listener\RouteCompiledInterface;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\Resource\ResourceInterface;
use Stasis\Tests\Unit\EventDispatcher\Event\EventTestCase;

class RouteCompiledEventTest extends EventTestCase
{
    private CompiledRoute $route;

    public function setUp(): void
    {
        parent::setUp();
        $this->route = new CompiledRoute(
            '/page',
            '/page.html',
            $this->createMock(ResourceInterface::class),
        );
    }

    protected function getEvent(): EventInterface
    {
        return new RouteCompiledEvent($this->route);
    }

    protected function getEventData(): mixed
    {
        return new RouteCompiledData($this->route);
    }

    protected function getListenerClass(): string
    {
        return RouteCompiledInterface::class;
    }

    protected function getListenerMethod(): string
    {
        return 'onRouteCompiled';
    }
}
