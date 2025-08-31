<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Event\RouteGenerated;

use Vstelmakh\Stasis\Event\EventInterface;
use Vstelmakh\Stasis\Event\ListenerInterface;
use Vstelmakh\Stasis\Router\Compiler\CompiledRoute;

readonly class RouteGenerated implements EventInterface
{
    public function __construct(
        private CompiledRoute $route,
    ) {}

    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof RouteGeneratedListenerInterface) {
            return false;
        }

        $listener->onRouteGenerated($this->route);
        return true;
    }
}
