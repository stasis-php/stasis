<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router\Compiler\RouteType;

use Vstelmakh\Stasis\Controller\ControllerInterface;

readonly class ControllerType implements TypeInterface
{
    public function __construct(
        /** @var class-string<ControllerInterface> */
        public string $class,
        public array $parameters = [],
    ) {}

    public function accept(TypeVisitorInterface $visitor): void
    {
        $visitor->visitController($this);
    }
}
