<?php

declare(strict_types=1);

namespace Stasis\Console;

use Stasis\Kernel;
use Symfony\Component\Console\Command\Command;

interface CommandFactoryInterface
{
    /**
     * @return string Name of the command which will be used to call it.
     */
    public static function name(): string;

    /**
     * @return string Description of the command.
     */
    public static function description(): string;

    /**
     * Static factory method to create a new instance of the command.
     */
    public static function create(Kernel $kernel): Command;
}
