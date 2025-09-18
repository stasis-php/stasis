<?php

declare(strict_types=1);

namespace Stasis\Dispatcher\Event\RouteGenerated;

use Stasis\Dispatcher\EventInterface;
use Stasis\Dispatcher\Listener\RouteGeneratedInterface;
use Stasis\Dispatcher\ListenerInterface;
use Stasis\Router\Compiler\CompiledRoute;

readonly class RouteGeneratedEvent implements EventInterface
{
    public function __construct(
        public CompiledRoute $route,
    ) {}

    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof RouteGeneratedInterface) {
            return false;
        }

        $data = new RouteGeneratedData($this->route);
        $listener->onRouteGenerated($data);
        return true;
    }
}
