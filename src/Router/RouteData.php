<?php

declare(strict_types=1);

namespace Stasis\Router;

use Stasis\Router\Compiler\CompiledRoute;

readonly class RouteData
{
    public function __construct(
        public string $path,
        public ?string $name,
    ) {}

    public static function fromCompiled(CompiledRoute $route): self
    {
        return new self($route->path, $route->name);
    }
}
