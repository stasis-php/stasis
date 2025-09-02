<?php

declare(strict_types=1);

namespace Stasis\Router\Route;

use Stasis\Controller\ControllerInterface;

readonly class Route implements RouteInterface
{
    /**
     * A route defines an endpoint accessible to the end user through a specified path.
     * Any parameters declared in the route are passed to the controller’s render method.
     *
     * @param string $path Route path, starting with slash.
     * @param ControllerInterface|string|\Closure $controller Controller instance, service reference implementing ControllerInterface, or closure.
     * @param ?string $name Route name. Must be unique within all defined routes.
     * @param array<string, mixed> $parameters Route parameters. Passed to the controller render call.
     */
    public function __construct(
        public string $path,
        public ControllerInterface|string|\Closure $controller,
        public ?string $name = null,
        public array $parameters = [],
    ) {}

    public function accept(RouteVisitorInterface $visitor): void
    {
        $visitor->visitRoute($this);
    }
}
