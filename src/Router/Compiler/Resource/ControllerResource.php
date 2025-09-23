<?php

declare(strict_types=1);

namespace Stasis\Router\Compiler\Resource;

use Stasis\Controller\ControllerInterface;

readonly class ControllerResource implements ResourceInterface
{
    public function __construct(
        public ControllerInterface|string|\Closure $reference,
        /** @var array<string, mixed> */
        public array $parameters = [],
    ) {}

    public function accept(ResourceVisitorInterface $visitor): void
    {
        $visitor->visitController($this);
    }
}
