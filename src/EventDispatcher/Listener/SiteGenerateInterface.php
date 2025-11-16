<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher\Listener;

use Stasis\EventDispatcher\Event\SiteGenerate\SiteGenerateData;
use Stasis\EventDispatcher\ListenerInterface;

/**
 * Interface for listeners that handle site generation event. Triggered before the site is generated.
 * May be used to add global interactions with router, for example, to register a template function for routes.
 */
interface SiteGenerateInterface extends ListenerInterface
{
    public function onSiteGenerate(SiteGenerateData $data): void;
}
