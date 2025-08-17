<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Generator;

use Vstelmakh\Stasis\Generator\Distribution\DistributionInterface;
use Vstelmakh\Stasis\Router\Compiler\CompiledRoute;
use Vstelmakh\Stasis\Router\Compiler\CompiledRouteCollection;
use Vstelmakh\Stasis\ServiceLocator\ServiceLocator;

class SiteGenerator
{
    public function __construct(
        private readonly ServiceLocator $serviceLocator,
        private readonly DistributionInterface $distribution,
    ) {}

    public function generate(CompiledRouteCollection $routes): void
    {
        $this->distribution->clear();

        foreach ($routes as $route) {
            $this->processRoute($route);
        }
    }

    private function processRoute(CompiledRoute $route): void
    {
        $visitor = new SiteGeneratorVisitor($route->path, $this->serviceLocator, $this->distribution);
        $route->type->accept($visitor);
    }
}
