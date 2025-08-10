<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

class CompiledRoutes
{
    /** @var array<string, CompiledRoute> */
    private array $routeByPath = [];

    /** @var array<string, CompiledRoute> */
    private array $routeByName = [];

    public function getByName(string $name): ?CompiledRoute
    {
        return $this->routeByName[$name] ?? null;
    }

    /**
     * @return array<CompiledRoute>
     */
    public function getAll(): array
    {
        return array_values($this->routeByPath);
    }

    /**
     * @throws CompilationFailedException
     */
    public function add(CompiledRoute $route): self
    {
        $this->addToPathMap($route);
        $this->addToNameMap($route);
        return $this;
    }

    /**
     * @throws CompilationFailedException
     */
    private function addToPathMap(CompiledRoute $route): void
    {
        $path = $route->path;

        if (isset($this->routeByPath[$path])) {
            $this->throwCompilationFailedException($route, sprintf('Duplicated route path "%s".', $path));
        }

        $this->routeByPath[$path] = $route;
    }

    /**
     * @throws CompilationFailedException
     */
    private function addToNameMap(CompiledRoute $route): void
    {
        $name = $route->name;

        if ($name === null) {
            return;
        }

        if (isset($this->routeByName[$name])) {
            $this->throwCompilationFailedException($route, sprintf('Duplicated route named "%s".', $name));
        }

        $this->routeByName[$name] = $route;
    }

    /**
     * @throws CompilationFailedException
     */
    private function throwCompilationFailedException(CompiledRoute $route, string $message): never
    {
        throw new CompilationFailedException($message, [
            'path' => $route->path,
            'name' => $route->name,
            'controller' => $route->controller,
        ]);
    }
}
