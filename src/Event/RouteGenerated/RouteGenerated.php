<?php

declare(strict_types=1);

namespace Stasis\Event\RouteGenerated;

use Stasis\Event\EventInterface;
use Stasis\Event\ListenerInterface;
use Stasis\Router\Compiler\CompiledRoute;

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
