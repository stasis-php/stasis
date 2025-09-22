<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Listener;

use Stasis\EventDispatcher\Event\RouteCompiled\RouteCompiledData;
use Stasis\EventDispatcher\ListenerInterface;

/**
 * Interface for listeners that handle route compilation events. Listener triggers on each route compilation.
 * Could be useful to collect information about all routes registered in the application. For example,
 * to generate a sitemap.
 */
interface RouteCompiledInterface extends ListenerInterface
{
    public function onRouteCompiled(RouteCompiledData $data): void;
}
