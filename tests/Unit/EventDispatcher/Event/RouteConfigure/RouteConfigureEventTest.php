<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher\Event\RouteConfigure;

use Stasis\EventDispatcher\Event\RouteConfigure\RouteConfigureData;
use Stasis\EventDispatcher\Event\RouteConfigure\RouteConfigureEvent;
use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\Listener\RouteConfigureInterface;
use Stasis\Router\Source\RouteSourceCollection;
use Stasis\Tests\Unit\EventDispatcher\Event\EventTestCase;

class RouteConfigureEventTest extends EventTestCase
{
    private RouteSourceCollection $routes;

    public function setUp(): void
    {
        parent::setUp();
        $this->routes = new RouteSourceCollection();
    }

    protected function getEvent(): EventInterface
    {
        return new RouteConfigureEvent($this->routes);
    }

    protected function getEventData(): mixed
    {
        return new RouteConfigureData($this->routes);
    }

    protected function getListenerClass(): string
    {
        return RouteConfigureInterface::class;
    }

    protected function getListenerMethod(): string
    {
        return 'onRouteConfigure';
    }
}
