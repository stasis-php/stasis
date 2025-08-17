<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Psr\Container\ContainerInterface;
use Vstelmakh\Stasis\Exception\LogicException;
use Vstelmakh\Stasis\Router\Compiler\RouteType\ControllerType;
use Vstelmakh\Stasis\Router\Route\Group;
use Vstelmakh\Stasis\Router\Route\Route;
use Vstelmakh\Stasis\Router\Route\RouteProviderInterface;
use Vstelmakh\Stasis\Router\Route\RouteVisitorInterface;

class RouteCompilerVisitor implements RouteVisitorInterface
{
    public function __construct(
        private readonly string $basePath,
        private readonly ContainerInterface $container,
        public readonly CompiledRouteCollection $routes = new CompiledRouteCollection(),
    ) {}

    public function visitRoute(Route $route): void
    {
        $path = $this->getCanonicalPath($route->path);
        $type = new ControllerType($route->controller, $route->parameters);
        $name = $route->name;

        $compiledRoute = new CompiledRoute($path, $type, $name);
        $this->routes->add($compiledRoute);
    }

    public function visitGroup(Group $group): void
    {
        $path = $this->getCanonicalPath($group->path);

        if (is_string($group->routes)) {
            $providerId = $group->routes;

            try {
                $provider = $this->container->get($providerId);
            } catch (\Throwable $exception) {
                throw new LogicException(sprintf(
                    'Error compiling route group with path "%s". Unable to resolve route provider "%s" from container.',
                    $path,
                    $providerId,
                ));
            }

            if (!$provider instanceof RouteProviderInterface) {
                throw new LogicException(sprintf(
                    'Error compiling route group with path "%s". Specified provider "%s" does not implement "%s".',
                    $path,
                    $providerId,
                    RouteProviderInterface::class,
                ));
            }

            $routes = $provider->routes();
        } else {
            $routes = $group->routes;
        }

        $visitor = new self($path, $this->container, $this->routes);
        foreach ($routes as $route) {
            $visitor->visitRoute($route);
        }
    }

    private function getCanonicalPath(string $path): string
    {
        $path = $this->basePath . $path;
        $path = preg_replace('/\/{2,}/', '/', $path);
        $path = rtrim($path, '/');
        return $path === '' ? '/' : $path;
    }
}
