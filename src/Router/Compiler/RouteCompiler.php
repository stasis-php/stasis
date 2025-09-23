<?php

declare(strict_types=1);

namespace Stasis\Router\Compiler;

use Stasis\EventDispatcher\EventDispatcher;
use Stasis\Router\Route\RouteInterface;
use Stasis\ServiceLocator\ServiceLocator;

/**
 * @internal
 */
class RouteCompiler
{
    public function __construct(
        private readonly string $basePath,
        private readonly ServiceLocator $serviceLocator,
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @param iterable<RouteInterface> $routes
     */
    public function compile(iterable $routes): CompiledRouteCollection
    {
        $visitor = new RouteCompilerVisitor($this->basePath, $this->serviceLocator, $this->eventDispatcher);

        foreach ($routes as $route) {
            $route->accept($visitor);
        }

        return $visitor->routes;
    }
}
