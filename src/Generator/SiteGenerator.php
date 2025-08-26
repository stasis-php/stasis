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

    public function generate(CompiledRouteCollection $routes, bool $symlinkFiles): void
    {
        $currentRouteContainer = new RouteContainer();
        $router = new Router($routes, $currentRouteContainer);

        $this->distribution->clear();

        foreach ($routes as $route) {
            $this->processRoute($router, $currentRouteContainer, $route, $symlinkFiles);
        }
    }

    private function processRoute(
        Router $router,
        RouteContainer $currentRouteContainer,
        CompiledRoute $route,
        bool $symlinkFiles,
    ): void {
        $currentRouteContainer->route = $route;

        $visitor = new SiteGeneratorVisitor(
            $this->serviceLocator,
            $this->distribution,
            $router,
            $route->distPath,
            $symlinkFiles,
        );

        $route->resource->accept($visitor);
    }
}
