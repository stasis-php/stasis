<?php

declare(strict_types=1);

namespace Stasis\Router\Route;

readonly class Group implements RouteInterface
{
    /**
     * A collection of routes that share a common root path.
     *
     * @param string $path Root path of the group, starting with slash. All routes in the group will be prefixed with this path.
     * @param iterable<RouteInterface>|RouteProviderInterface|string $routes List of routes, route provider instance, or service reference implementing RouteProviderInterface.
     */
    public function __construct(
        public string $path,
        public iterable|RouteProviderInterface|string $routes = [],
    ) {}

    public function accept(RouteVisitorInterface $visitor): void
    {
        $visitor->visitGroup($this);
    }
}
