<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator;

use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Router\Compiler\CompiledRoute;
use Vstelmakh\Stasis\Router\Compiler\CompiledRouteCollection;
use Vstelmakh\Stasis\Router\RouteContainer;
use Vstelmakh\Stasis\Router\Router;
use Vstelmakh\Stasis\ServiceLocator\ServiceLocator;

class SiteGenerator
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
        private readonly DistributionInterface $distribution,
    ) {}

    public function generate(CompiledRouteCollection $routes): void
    {
        $routeContainer = new RouteContainer();
        $router = new Router($routes, $routeContainer);

        $this->distribution->clear();

        foreach ($routes as $route) {
            $this->processRoute($router, $routeContainer, $route);
        }
    }

    private function processRoute(Router $router, RouteContainer $routeContainer, CompiledRoute $route): void
    {
        $routeContainer->route = $route;
        $visitor = new SiteGeneratorVisitor($route->path, $this->serviceLocator, $this->distribution, $router);
        $route->resource->accept($visitor);
    }
}
