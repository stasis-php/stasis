<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterConfig;

use Stasis\EventDispatcher\ListenerInterface;

/**
 * Interface for listeners that handle route configuration event.
 *
 * This listener is triggered during the route configuration phase, allowing extensions
 * and plugins to dynamically register additional routes to the application.
 */
interface RouterConfigListenerInterface extends ListenerInterface
{
    public function onRouterConfig(RouterConfigData $data): void;
}
