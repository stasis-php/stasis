<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterConfig;

use Stasis\Exception\RuntimeException;
use Stasis\Router\Route\RouteInterface;
use Stasis\Router\Source\RouteSource;
use Stasis\Router\Source\RouteSourceCollection;

class RouterConfigData
{
    /**
     * @internal
     */
    public function __construct(
        private readonly RouteSourceCollection $sources,
    ) {}

    /**
     * Register additional routes to the application. Accepts an iterable of {@see RouteInterface} instances,
     * following the same format as {@see ConfigInterface::routes}.
     *
     * Note: This method should be called only once per listener.
     *
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
