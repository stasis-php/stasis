<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterInitialized;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Router;

/**
 * @internal
 */
class RouterInitializedEvent implements EventInterface
{
    public function __construct(
        private readonly Router $routes,
    ) {}

    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof ListenRouterInitializedInterface) {
            return false;
        }

        $data = new RouterInitializedData($this->routes);
        $listener->onRouterInitialized($data);
        return true;
    }
}
