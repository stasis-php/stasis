<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\RouterInitialized;

use Stasis\Router\Router;

class RouterInitializedData
{
    public function __construct(
        public readonly Router $router,
    ) {}
}
