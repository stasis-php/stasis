<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler\Resource;

use Vstelmakh\Stasis\Controller\ControllerInterface;

readonly class ControllerResource implements ResourceInterface
{
    public function __construct(
        /** @var class-string<ControllerInterface> */
        public string $class,
        public array $parameters = [],
    ) {}

    public function accept(ResourceVisitorInterface $visitor): void
    {
        $visitor->visitController($this);
    }
}
