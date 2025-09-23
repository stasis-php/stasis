<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Event\RouteCompiled;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\Listener\RouteCompiledInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Compiler\CompiledRoute;

/**
 * @internal
 */
class RouteCompiledEvent implements EventInterface
{
    public function __construct(
        public CompiledRoute $route,
    ) {}

    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof RouteCompiledInterface) {
            return false;
        }

        $data = new RouteCompiledData($this->route);
        $listener->onRouteCompiled($data);
        return true;
    }
}
