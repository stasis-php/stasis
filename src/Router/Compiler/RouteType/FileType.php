<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler\RouteType;

readonly class FileType implements TypeInterface
{
    public function __construct(
        public string $source,
    ) {}

    public function accept(TypeVisitorInterface $visitor): void
    {
        $visitor->visitFile($this);
    }
}
