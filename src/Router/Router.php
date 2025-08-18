<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router;

use Vstelmakh\Stasis\Router\Compiler\CompiledRouteCollection;

class Router
{
    public function __construct(
        private readonly CompiledRouteCollection $compiledRoutes,
        private readonly RouteContainer $routeContainer,
    ) {}

    public function get(string $name): RouteData
    {
        $route = $this->compiledRoutes->getByName($name);

        if ($route === null) {
            throw new \LogicException(sprintf('Route with name "%s" not found.', $name));
        }

        return RouteData::fromCompiled($route);
    }

    public function current(): RouteData
    {
        $route = $this->routeContainer->route ?? null;

        if ($route === null) {
            throw new \LogicException('Current route is not set.');
        }

        return RouteData::fromCompiled($route);
    }
}
