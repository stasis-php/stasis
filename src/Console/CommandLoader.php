<?php

declare(strict_types=1);

namespace Stasis\Console;

use Stasis\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LazyCommand;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

class CommandLoader implements CommandLoaderInterface
{
    /** @var array<string, class-string<CommandFactoryInterface> */
    private array $map = [];

    /**
     * @param array<class-string<CommandFactoryInterface>> $factories
     */
    public function __construct(
        private readonly Kernel $kernel,
        array $factories,
    ) {
        foreach ($factories as $factory) {
            $this->add($factory);
        }
    }

    public function get(string $name): Command
    {
        $factory = $this->map[$name] ?? null;
        if ($factory === null) {
            throw new \InvalidArgumentException(sprintf('Command with name "%s" not found.', $name));
        }

        return $this->createLazyCommand($factory);
    }

    public function has(string $name): bool
    {
        return isset($this->map[$name]);
    }

    public function getNames(): array
    {
        return array_keys($this->map);
    }

    /**
     * @param class-string<CommandFactoryInterface> $factory
     */
    private function add(string $factory): void
    {
        $this->validateFactoryClass($factory);
        $name = $factory::name();
        $this->map[$name] = $factory;
    }

    private function validateFactoryClass(string $factory): void
    {
        $isValidClass = is_subclass_of($factory, CommandFactoryInterface::class);

        if (!$isValidClass) {
            $message = sprintf('Factory "%s" must implement "%s" interface.', $factory, CommandFactoryInterface::class);
            throw new \InvalidArgumentException($message);
        }
    }

    /**
     * @param class-string<CommandFactoryInterface> $factory
     */
    private function createLazyCommand(string $factory): LazyCommand
    {
        $name = $factory::name();
        $description = $factory::description();
        $callable = fn() => $factory::create($this->kernel);
        return new LazyCommand($name, [], $description, false, $callable);
    }
}
