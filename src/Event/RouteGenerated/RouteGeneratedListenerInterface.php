<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Event\RouteGenerated;

use Vstelmakh\Stasis\Event\ListenerInterface;
use Vstelmakh\Stasis\Router\Compiler\CompiledRoute;

interface RouteGeneratedListenerInterface extends ListenerInterface
{
    public function onRouteGenerated(CompiledRoute $route): void;
}
