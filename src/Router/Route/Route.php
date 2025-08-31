<?php

declare(strict_types=1);

namespace Stasis\Router\Route;

use Stasis\Controller\ControllerInterface;

/**
 * Route is an endpoint that will be served to the end user via the provided path.
 * Parameters defined for the route are passed to the controller render call.
 */
readonly class Route implements RouteInterface
{
    public function __construct(
        public string $path,
        /** @var class-string<ControllerInterface> */
        public string $controller,
        public ?string $name = null,
        public array $parameters = [],
    ) {}

    public function accept(RouteVisitorInterface $visitor): void
    {
        $visitor->visitRoute($this);
    }
}
