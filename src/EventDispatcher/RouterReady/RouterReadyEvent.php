<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterReady;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Router;

/**
 * @internal
 */
class RouterReadyEvent implements EventInterface
{
    public function __construct(
        private readonly Router $routes,
    ) {}

    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof RouterReadyListenerInterface) {
            return false;
        }

        $data = new RouterReadyData($this->routes);
        $listener->onRouterReady($data);
        return true;
    }
}
