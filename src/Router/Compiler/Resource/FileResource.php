<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler\Resource;

readonly class FileResource implements ResourceInterface
{
    public function __construct(
        public string $source,
    ) {}

    public function accept(ResourceVisitorInterface $visitor): void
    {
        $visitor->visitFile($this);
    }
}
