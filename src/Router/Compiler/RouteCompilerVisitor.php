<?php

declare(strict_types=1);

namespace Stasis\Router\Compiler;

use Stasis\EventDispatcher\Event\RouteCompiled\RouteCompiledEvent;
use Stasis\EventDispatcher\EventDispatcher;
use Stasis\Exception\LogicException;
use Stasis\Router\Compiler\Resource\ControllerResource;
use Stasis\Router\Compiler\Resource\FileResource;
use Stasis\Router\Route\Asset;
use Stasis\Router\Route\Group;
use Stasis\Router\Route\Route;
use Stasis\Router\Route\RouteProviderInterface;
use Stasis\Router\Route\RouteVisitorInterface;
use Stasis\ServiceLocator\ServiceLocator;

class RouteCompilerVisitor implements RouteVisitorInterface
{
    public function __construct(
        private readonly string $basePath,
        private readonly ServiceLocator $serviceLocator,
        private readonly EventDispatcher $eventDispatcher,
        public readonly CompiledRouteCollection $routes = new CompiledRouteCollection(),
    ) {}

    public function visitRoute(Route $route): void
    {
        $canonicalPath = $this->getCanonicalPath($route->path);
        $distPath = $canonicalPath . '/index.html';
        $type = new ControllerResource($route->controller, $route->parameters);
        $name = $route->name;

        $compiledRoute = new CompiledRoute($canonicalPath, $distPath, $type, $name);
        $this->routes->add($compiledRoute);
        $this->dispatchRouteCompiled($compiledRoute);
    }

    public function visitGroup(Group $group): void
    {
        $path = $this->getCanonicalPath($group->path);
        $routes = $this->getRoutes($group->routes);
        $visitor = new self($path, $this->serviceLocator, $this->eventDispatcher, $this->routes);

        foreach ($routes as $route) {
            $route->accept($visitor);
        }
    }

    public function visitAsset(Asset $asset): void
    {
        $canonicalPath = $this->getCanonicalPath($asset->path);
        $resource = new FileResource($asset->sourcePath);
        $name = $asset->name;

        $compiledRoute = new CompiledRoute($canonicalPath, $canonicalPath, $resource, $name);
        $this->routes->add($compiledRoute);
        $this->dispatchRouteCompiled($compiledRoute);
    }

    /**
     * @return iterable<Route>
     */
    private function getRoutes(iterable|RouteProviderInterface|string $provider): iterable
    {
        if (is_iterable($provider)) {
            return $provider;
        }

        if ($provider instanceof RouteProviderInterface) {
            return $provider->routes();
        }

        if (is_string($provider)) {
            $instance = $this->serviceLocator->get($provider, RouteProviderInterface::class);
            return $instance->routes();
        }

        throw new LogicException(sprintf(
            'Unexpected provider type "%s". Expected container reference, instance of %s or Closure".',
            get_debug_type($provider),
            RouteProviderInterface::class,
        ));
    }

    private function getCanonicalPath(string $path): string
    {
        return PathNormalizer::normalize($this->basePath . '/' . $path);
    }

    private function dispatchRouteCompiled(CompiledRoute $route): void
    {
        $event = new RouteCompiledEvent($route);
        $this->eventDispatcher->dispatch($event);
    }
}
