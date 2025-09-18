<?php

declare(strict_types=1);

namespace Stasis\Dispatcher\Listener;

use Stasis\Dispatcher\Event\RouteGenerated\RouteGeneratedData;
use Stasis\Dispatcher\ListenerInterface;

interface RouteGeneratedInterface extends ListenerInterface
{
    public function onRouteGenerated(RouteGeneratedData $event): void;
}
