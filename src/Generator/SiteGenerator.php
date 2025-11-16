<?php

declare(strict_types=1);

namespace Stasis\Generator;

use Stasis\EventDispatcher\Event\SiteGenerate\SiteGenerateEvent;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\Generator\Distribution\DistributionInterface;
use Stasis\Router\Compiler\CompiledRoute;
use Stasis\Router\Compiler\CompiledRouteCollection;
use Stasis\Router\RouteContainer;
use Stasis\Router\Router;
use Stasis\ServiceLocator\ServiceLocator;

/**
 * @internal
 */
class SiteGenerator
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
        private readonly DistributionInterface $distribution,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    public function generate(CompiledRouteCollection $routes, bool $symlinkFiles): void
    {
        $currentRouteContainer = new RouteContainer();
        $router = new Router($routes, $currentRouteContainer);

        $event = new SiteGenerateEvent($router);
        $this->eventDispatcher->dispatch($event);

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
