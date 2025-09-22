<?php

declare(strict_types=1);

namespace Stasis\Extension;

use Stasis\EventDispatcher\ListenerInterface;

interface ExtensionInterface
{
    /**
     * Returns the listeners that will be registered.
     * @return iterable<ListenerInterface>
     */
    public function listeners(): iterable;
}
