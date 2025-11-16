<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Event\SiteGenerate;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\Listener\SiteGenerateInterface;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Router\Router;

/**
 * @internal
 */
class SiteGenerateEvent implements EventInterface
{
    public function __construct(
        private readonly Router $router,
    ) {}

    public function accept(ListenerInterface $listener): bool
    {
        if (!$listener instanceof SiteGenerateInterface) {
            return false;
        }

        $data = new SiteGenerateData($this->router);
        $listener->onSiteGenerate($data);
        return true;
    }
}
