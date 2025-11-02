<?php

declare(strict_types=1);

namespace Stasis\Tests\Functional\Generate\source;

use Stasis\Controller\ControllerInterface;
use Stasis\Router\Router;

class CurrentTimeController implements ControllerInterface
{
    public function render(Router $router, array $parameters): string
    {
        $output = [];

        $home = $router->get('home');
        $output[] = sprintf('<a href="%s">Home</a>', $home->path);

        $about = $router->get('about');
        $output[] = sprintf('<a href="%s">About</a>', $about->path);

        $format = $parameters['format'];
        $output[] = sprintf('Current time: %s', date($format));

        return implode("\n", $output);
    }
}
