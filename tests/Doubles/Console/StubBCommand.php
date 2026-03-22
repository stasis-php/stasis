<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\Console;

use Stasis\Console\CommandFactoryInterface;
use Stasis\Kernel;
use Symfony\Component\Console\Command\Command;

class StubBCommand extends Command implements CommandFactoryInterface
{
    private const string NAME = 'test:b';

    #[\Override]
    public static function name(): string
    {
        return self::NAME;
    }

    #[\Override]
    public static function description(): string
    {
        return 'Test command B';
    }

    #[\Override]
    public static function create(Kernel $kernel): Command
    {
        return new self(self::NAME);
    }
}
