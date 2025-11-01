<?php

declare(strict_types=1);

namespace Stasis\Tests\Doubles\Console;

use Stasis\Console\CommandFactoryInterface;
use Stasis\Kernel;
use Symfony\Component\Console\Command\Command;

class StubACommand extends Command implements CommandFactoryInterface
{
    private const string NAME = 'test:a';

    public static function name(): string
    {
        return self::NAME;
    }

    public static function description(): string
    {
        return 'Test command A';
    }

    public static function create(Kernel $kernel): Command
    {
        return new self(self::NAME);
    }
}
