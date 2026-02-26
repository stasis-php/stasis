<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterReady;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Router;

/**
 * @internal
 */
class RouterReadyEvent implements EventInterface
{
    public function __construct(
        private readonly Router $router,
        /** @var iterable<CompiledRoute> */
        private readonly iterable $routes,
    ) {}

    #[\Override]
    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof RouterReadyListenerInterface) {
            return false;
        }

        $data = new RouterReadyData($this->router, $this->routes);
        $listener->onRouterReady($data);
        return true;
    }
}
