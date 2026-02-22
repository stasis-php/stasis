<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterReady;

use Stasis\Router\Router;

class RouterReadyData
{
    public function __construct(
        public readonly Router $router,
    ) {}
}
