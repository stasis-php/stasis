<?php

declare(strict_types=1);

namespace Stasis\Tests\EventDispatcher\Test;

use Stasis\EventDispatcher\EventInterface;
use Stasis\EventDispatcher\ListenerInterface;

abstract class AbstractTestEvent implements EventInterface
{
    /** @var list<class-string> */
    public private(set) array $acceptedWith = [];

    /** @return class-string<AbstractTestListener> */
    abstract public function listenerClass(): string;

    public function accept(ListenerInterface $listener): bool
    {
        $this->acceptedWith[] = $listener::class;

        $class = $this->listenerClass();
        if (!$listener instanceof $class) {
            return false;
        }

        $listener->onTestEvent();
        return true;
    }
}
