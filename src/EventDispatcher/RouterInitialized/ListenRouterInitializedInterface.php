<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterInitialized;

use Stasis\EventDispatcher\ListenerInterface;

/**
 * Interface for listeners that handle router initialization event.
 *
 * This listener is triggered after all routes have been compiled and the router is initialized. It provides access to
 * the same as router instance as in controllers, making it useful for operations that require the full route registry,
 * such as adding global router interactions, e.g., registering template functions for routes, generating sitemaps,
 * or performing route analysis.
 */
interface ListenRouterInitializedInterface extends ListenerInterface
{
    public function onRouterInitialized(RouterInitializedData $data): void;
}
