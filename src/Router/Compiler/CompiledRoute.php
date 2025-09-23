<?php

declare(strict_types=1);

namespace Stasis\Router\Compiler;

use Stasis\Router\Compiler\Resource\ResourceInterface;

readonly class CompiledRoute
{
    /**
     * @internal
     */
    public function __construct(
        public string $path,
        public string $distPath,
        public ResourceInterface $resource,
        public ?string $name = null,
    ) {}
}
