<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher;

interface EventInterface
{
    /**
     * @return bool true if the listener was accepted, false otherwise
     */
    public function accept(ListenerInterface $listener): bool;
}
