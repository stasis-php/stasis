<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Event\RouteConfigure;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\Listener\RouteConfigureInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Source\RouteSourceCollection;

class RouteConfigureEvent implements EventInterface
{
    public function __construct(
        private readonly RouteSourceCollection $sources,
    ) {}

    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof RouteConfigureInterface) {
            return false;
        }

        $data = new RouteConfigureData($this->sources);
        $listener->onRouteConfigure($data);
        return true;
    }
}
