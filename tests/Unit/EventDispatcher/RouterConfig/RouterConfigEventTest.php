<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\EventDispatcher\RouterConfig;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\RouterConfig\RouterConfigData;
use Stasis\EventDispatcher\RouterConfig\RouterConfigEvent;
use Stasis\EventDispatcher\RouterConfig\RouterConfigListenerInterface;
use Stasis\Router\Source\RouteSourceCollection;
use Stasis\Tests\Unit\EventDispatcher\EventTestCase;

class RouterConfigEventTest extends EventTestCase
{
    private RouteSourceCollection $routes;

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->routes = new RouteSourceCollection();
    }

    #[\Override]
    protected function getEvent(): EventInterface
    {
        return new RouterConfigEvent($this->routes);
    }

    #[\Override]
    protected function getEventData(): mixed
    {
        return new RouterConfigData($this->routes);
    }

    #[\Override]
    protected function getListenerClass(): string
    {
        return RouterConfigListenerInterface::class;
    }

    #[\Override]
    protected function getListenerMethod(): string
    {
        return 'onRouterConfig'; /* @see RouterConfigListenerInterface::onRouterConfig */
    }
}
