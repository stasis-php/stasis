<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Vstelmakh\Stasis\Router\Compiler\RouteType\TypeInterface;

readonly class CompiledRoute
{
    public function __construct(
        public string $path,
        public TypeInterface $type,
        public ?string $name = null,
    ) {}
}
