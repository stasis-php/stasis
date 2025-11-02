<?php

declare(strict_types=1);

namespace Stasis\Tests\Functional\Generate;

use PHPUnit\Framework\TestCase;
use Stasis\Tests\Functional\StasisProcessFactory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class GenerateTest extends TestCase
{
    private Filesystem $filesystem;
    private Process $generate;

    public function setUp(): void
    {
        $this->filesystem = new Filesystem();
        $this->generate = StasisProcessFactory::create([
            '--config', __DIR__ . '/source/config.php',
            'generate',
        ]);
    }

    public function testGenerate(): void
    {
        $this->removeDist();
        $this->createDistFile('unexpected.html');
        $this->runGenerateCommand();
        $this->assertDistHome();
        $this->assertDistAbout();
        $this->assertDistTime();
        $this->assertDistBlog();
        $this->assertDistAssets();
        $this->assertDistMissing('unexpected.html');
    }

    private function removeDist(): void
    {
        $this->filesystem->remove(__DIR__ . '/dist');
    }

    private function createDistFile(string $filename, string $content = ''): void
    {
        $filePath = __DIR__ . '/dist/' . $filename;
        $this->filesystem->dumpFile($filePath, $content);
    }

    private function runGenerateCommand(): void
    {
        $this->generate->run();
        $exitCode = $this->generate->getExitCode();
        $output = $this->generate->getOutput();

        self::assertSame(0, $exitCode, 'Command returned non-zero exit code.');
        self::assertStringContainsString(
            'Generated successfully',
            $output,
            'Missing success message in output.',
        );
    }

    private function assertDistHome(): void
    {
        self::assertFileMatchesFormat(
            'Hello World!',
            __DIR__ . '/dist/index.html',
            'Home: Unexpected page content.',
        );
    }

    private function assertDistAbout(): void
    {
        self::assertFileMatchesFormat(
            'Stasis is cool!',
            __DIR__ . '/dist/about.html',
            'About: Unexpected page content.',
        );
    }

    private function assertDistTime(): void
    {
        $pathTime = __DIR__ . '/dist/time.html';
        self::assertFileExists($pathTime);

        /** @var non-empty-string $timeContent */
        $timeContent = file_get_contents($pathTime);
        $timeLines = explode("\n", $timeContent);

        self::assertSame('<a href="/">Home</a>', $timeLines[0], 'Time: Missing home link.');
        self::assertSame('<a href="/about.html">About</a>', $timeLines[1], 'Time: Missing about link.');
        self::assertMatchesRegularExpression(
            '/Current time: \d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',
            $timeLines[2],
            'Time: Unexpected time format.',
        );
    }

    private function assertDistBlog(): void
    {
        self::assertFileMatchesFormat(
            'Article 1',
            __DIR__ . '/dist/blog/article1.html',
            'Blog: Unexpected Article 1 content.',
        );

        self::assertFileMatchesFormat(
            'Article 2',
            __DIR__ . '/dist/blog/article2.html',
            'Blog: Unexpected Article 2 content.',
        );
    }

    private function assertDistAssets(): void
    {
        self::assertFileMatchesFormat(
            '/* This is a test file */',
            __DIR__ . '/dist/style.css',
            'Asset: Unexpected style content.',
        );

        self::assertFileMatchesFormat(
            "Asset 1\n",
            __DIR__ . '/dist/assets/asset1.txt',
            'Asset: Unexpected Asset 1 content.',
        );

        self::assertFileMatchesFormat(
            "Asset 2\n",
            __DIR__ . '/dist/assets/asset2.txt',
            'Asset: Unexpected Asset 2 content.',
        );
    }

    private function assertDistMissing(string $filename): void
    {
        self::assertFileDoesNotExist(__DIR__ . '/dist/' . $filename);
    }
}
