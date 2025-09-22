<?php

declare(strict_types=1);

namespace Stasis\Extension;

use Stasis\EventDispatcher\EventDispatcher;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Exception\LogicException;

class ExtensionLoader
{
    public function __construct(
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @param iterable<ExtensionInterface> $extensions
     */
    public function load(iterable $extensions): void
    {
        foreach ($extensions as $extension) {
            $this->validateExtension($extension);
            $this->registerListeners($extension);
        }
    }

    private function validateExtension(mixed $extension): void
    {
        if (!$extension instanceof ExtensionInterface) {
            throw new LogicException(sprintf(
                'Unexpected extension of type "%s" provided. Extensions must implement %s.',
                get_debug_type($extension),
                ExtensionInterface::class,
            ));
        }
    }

    private function registerListeners(ExtensionInterface $extension): void
    {
        $listeners = $extension->listeners();

        foreach ($listeners as $listener) {
            $this->validateListener($listener);
            $this->eventDispatcher->add($listener);
        }
    }

    private function validateListener(mixed $listener): void
    {
        if (!$listener instanceof ListenerInterface) {
            throw new LogicException(sprintf(
                'Unexpected listener of type "%s" provided. Extension listeners must implement %s.',
                get_debug_type($listener),
                ListenerInterface::class,
            ));
        }
    }
}
