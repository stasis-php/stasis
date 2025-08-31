<?php

declare(strict_types=1);

namespace Stasis\Event\RouteGenerated;

use Stasis\Event\ListenerInterface;
use Stasis\Router\Compiler\CompiledRoute;

interface RouteGeneratedListenerInterface extends ListenerInterface
{
    public function onRouteGenerated(CompiledRoute $route): void;
}
