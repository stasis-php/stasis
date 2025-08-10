<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Vstelmakh\Stasis\Controller\ControllerInterface;

readonly class CompiledRoute
{
    public function __construct(
        public string $path,
        public string $distPath,
        /** @var class-string<ControllerInterface> */
        public string $controller,
        public ?string $name = null,
        public array $parameters = [],
    ) {}
}
