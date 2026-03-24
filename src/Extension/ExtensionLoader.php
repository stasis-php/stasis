<?php

declare(strict_types=1);

namespace Stasis\Extension;

use Stasis\EventDispatcher\EventDispatcher;
use Stasis\EventDispatcher\ListenerInterface;
use Stasis\Exception\LogicException;
use Stasis\ServiceLocator\ServiceLocator;

/**
 * @internal
 */
class ExtensionLoader
{
    public function __construct(
        private readonly EventDispatcher $eventDispatcher,
    ) {}

    /**
     * @param iterable<ExtensionInterface|string> $extensions
     */
    public function load(iterable $extensions, ServiceLocator $serviceLocator): void
    {
        foreach ($extensions as $extension) {
            $extension = $this->resolveExtension($extension, $serviceLocator);
            $this->validateExtension($extension);
            $this->registerListeners($extension);
        }
    }

    private function resolveExtension(
        mixed $extension,
        ServiceLocator $serviceLocator,
    ): ExtensionInterface {
        if ($extension instanceof ExtensionInterface) {
            return $extension;
        }

        if (is_string($extension)) {
            try {
                return $serviceLocator->get($extension, ExtensionInterface::class);
            } catch (\Throwable $exception) {
                throw new LogicException(
                    message: sprintf('Failed to load extension with container reference "%s".', $extension),
                    previous: $exception,
                );
            }
        }

        throw new LogicException(sprintf(
            'Invalid extension type "%s".'
            . ' Extension must be an instance of %s or a string reference to a service that implements %s.',
            get_debug_type($extension),
            ExtensionInterface::class,
            ExtensionInterface::class,
        ));
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
