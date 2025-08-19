<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler\Resource;

interface ResourceInterface
{
    public function accept(ResourceVisitorInterface $visitor): void;
}
