<?php

declare(strict_types=1);

namespace Stasis\Router\Source;

use Stasis\Router\Route\RouteInterface;

/**
 * @internal
 */
readonly class RouteSource
{
    /**
     * @param iterable<RouteInterface> $routes
     */
    public function __construct(
        public string $name,
        public iterable $routes,
    ) {}
}
