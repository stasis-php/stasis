<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Event\RouteConfigure;

use Stasis\Exception\RuntimeException;
use Stasis\Router\Route\RouteInterface;
use Stasis\Router\Source\RouteSource;
use Stasis\Router\Source\RouteSourceCollection;

class RouteConfigureData
{
    /**
     * @internal
     */
    public function __construct(
        private readonly RouteSourceCollection $sources,
    ) {}

    /**
     * Configure additional routes here. Accepts the same format as in ConfigInterface::routes().
     * Consider calling this method only once.
     * @see \Stasis\Config\ConfigInterface::routes()
     * @param iterable<RouteInterface> $routes
     */
    public function routes(iterable $routes): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $trace[1]['class'] ?? throw new RuntimeException('Unable to determine caller class.');

        $source = new RouteSource($caller, $routes);
        $this->sources->add($source);
    }
}
