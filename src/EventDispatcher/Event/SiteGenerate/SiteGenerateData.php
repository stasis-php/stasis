<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Event\SiteGenerate;

use Stasis\Router\Router;

class SiteGenerateData
{
    /**
     * @internal
     */
    public function __construct(
        public readonly Router $router,
    ) {}
}
