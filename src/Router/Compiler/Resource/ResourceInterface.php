<?php

declare(strict_types=1);

namespace Stasis\Router\Compiler\Resource;

/**
 * @internal
 */
interface ResourceInterface
{
    public function accept(ResourceVisitorInterface $visitor): void;
}
