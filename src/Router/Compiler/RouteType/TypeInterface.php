<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler\RouteType;

interface TypeInterface
{
    public function accept(TypeVisitorInterface $visitor): void;
}
