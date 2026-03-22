<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterConfig;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Source\RouteSourceCollection;

/**
 * @internal
 */
class RouterConfigEvent implements EventInterface
{
    public function __construct(
        private readonly RouteSourceCollection $sources,
    ) {}

    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof RouterConfigListenerInterface) {
            return false;
        }

        $data = new RouterConfigData($this->sources);
        $listener->onRouterConfig($data);
        return true;
    }
}
