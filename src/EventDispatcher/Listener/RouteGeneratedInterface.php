<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Listener;

use Stasis\EventDispatcher\Event\RouteGenerated\RouteGeneratedData;
use Stasis\EventDispatcher\ListenerInterface;

interface RouteGeneratedInterface extends ListenerInterface
{
    public function onRouteGenerated(RouteGeneratedData $event): void;
}
