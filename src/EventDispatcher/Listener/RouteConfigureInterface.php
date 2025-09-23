<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Listener;

use Stasis\EventDispatcher\Event\RouteConfigure\RouteConfigureData;
use Stasis\EventDispatcher\ListenerInterface;

/**
 * Interface for listeners that handle route configuration event. Triggered when routes are configured.
 * Used to add additional routes to the application.
 */
interface RouteConfigureInterface extends ListenerInterface
{
    public function onRouteConfigure(RouteConfigureData $data): void;
}
