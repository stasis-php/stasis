<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler;

use Vstelmakh\Stasis\Router\Compiler\Resource\ResourceInterface;

readonly class CompiledRoute
{
    public function __construct(
        public string $path,
        public string $distPath,
        public ResourceInterface $resource,
        public ?string $name = null,
    ) {}
}
