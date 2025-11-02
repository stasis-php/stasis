<?php

declare(strict_types=1);

namespace Stasis\Tests\Unit\Console;

use PHPUnit\Framework\TestCase;
use Stasis\Console\ApplicationFactory;
use Stasis\Kernel;

class ApplicationFactoryTest extends TestCase
{
    public function testCreateBuildsConfiguredApplication(): void
    {
        $kernel = $this->createMock(Kernel::class);
        $application = ApplicationFactory::create($kernel);

        self::assertSame('Stasis', $application->getName(), 'Unexpected application name.');

        self::assertTrue($application->get('help')->isHidden(), 'Help command visible.');
        self::assertTrue($application->get('list')->isHidden(), 'List command visible.');
        self::assertTrue($application->get('completion')->isHidden(), 'Completion command visible.');

        $definition = $application->getDefinition();
        $optionName = ltrim('--config', '-');
        self::assertTrue($definition->hasOption($optionName), 'Config option not defined.');
        self::assertSame(
            'stasis.php',
            $definition->getOption($optionName)->getDefault(),
            'Unexpected default config file name.',
        );

        $commandCount = count($application->all());
        self::assertSame(6, $commandCount, 'Unexpected number of commands.');
        // command count implemented to fail in case of new commands being added
        // this serves as a reminder to update commands below in case of new commands being added
        self::assertTrue($application->has('generate'), 'Generate command not found.');
        self::assertTrue($application->has('server'), 'Server command not found.');
    }
}
