<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Router;

use Vstelmakh\Stasis\Controller\ControllerInterface;

/**
 * Route is an endpoint which will be served to the end user via provided path.
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
