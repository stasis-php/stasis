<?php

declare(strict_types=1);

namespace Vstelmakh\Stasis\Controller;

use Vstelmakh\Stasis\Router\Router;

/**
 * Controller does the rendering of the provided page.
 */
interface ControllerInterface
{
    /**
     * @param array $parameters Defined on route and provided here as input.
     * @return string|resource Content of the rendered page.
     */
    public function render(Router $router, array $parameters);
}
