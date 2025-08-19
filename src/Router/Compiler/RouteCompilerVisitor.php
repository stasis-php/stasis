<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Vstelmakh\Stasis\Router\Compiler\Resource\ControllerResource;
use Vstelmakh\Stasis\Router\Compiler\Resource\FileResource;
use Vstelmakh\Stasis\Router\Route\Asset;
use Vstelmakh\Stasis\Router\Route\Group;
use Vstelmakh\Stasis\Router\Route\Route;
use Vstelmakh\Stasis\Router\Route\RouteProviderInterface;
use Vstelmakh\Stasis\Router\Route\RouteVisitorInterface;
use Vstelmakh\Stasis\ServiceLocator\ServiceLocator;

class RouteCompilerVisitor implements RouteVisitorInterface
{
    public function __construct(
        private readonly string $basePath,
        private readonly ServiceLocator $serviceLocator,
        public readonly CompiledRouteCollection $routes = new CompiledRouteCollection(),
    ) {}

    public function visitRoute(Route $route): void
    {
        $path = $this->getCanonicalPath($route->path);
        $type = new ControllerResource($route->controller, $route->parameters);
        $name = $route->name;

        $compiledRoute = new CompiledRoute($path, $type, $name);
        $this->routes->add($compiledRoute);
    }

    public function visitGroup(Group $group): void
    {
        $path = $this->getCanonicalPath($group->path);

        if (is_string($group->routes)) {
            $provider = $this->serviceLocator->get($group->routes, RouteProviderInterface::class);
            $routes = $provider->routes();
        } else {
            $routes = $group->routes;
        }

        $visitor = new self($path, $this->serviceLocator, $this->routes);
        foreach ($routes as $route) {
            $visitor->visitRoute($route);
        }
    }

    public function visitAsset(Asset $asset): void
    {
        $path = $this->getCanonicalPath($asset->path);
        $resource = new FileResource($asset->sourcePath, $path);
        $name = $asset->name;

        $compiledRoute = new CompiledRoute($path, $resource, $name);
        $this->routes->add($compiledRoute);
    }

    private function getCanonicalPath(string $path): string
    {
        $path = $this->basePath . $path;
        $path = preg_replace('/\/{2,}/', '/', $path);
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }
}
