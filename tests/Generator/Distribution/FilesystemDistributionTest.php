<?php

declare(strict_types=1);

namespace Stasis\Tests\Generator\Distribution;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Stasis\Exception\LogicException;
use Stasis\Exception\RuntimeException;
use Stasis\Generator\Distribution\FilesystemDistribution;
use Symfony\Component\Filesystem\Filesystem;

class FilesystemDistributionTest extends TestCase
{
    private const string BASE_PATH = __DIR__ . '/fake_dist';

    private MockObject&Filesystem $filesystem;
    private FilesystemDistribution $distribution;

    public function setUp(): void
    {
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->distribution = new FilesystemDistribution(self::BASE_PATH, $this->filesystem);
    }

    public function testRelativePath(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('is not absolute');
        new FilesystemDistribution('relative/path');
    }

    #[DataProvider('pathDataProvider')]
    public function testPath(string $path, string $expected): void
    {
        $distribution = new FilesystemDistribution($path, $this->filesystem);
        $actual = $distribution->path();
        self::assertSame($expected, $actual);
    }

    public static function pathDataProvider(): array
    {
        return [
            'canonical' => ['/path/to', '/path/to'],
            'following slash' => ['/path/to/', '/path/to'],
            'parent dir' => ['/path/../to', '/to'],
        ];
    }

    public function testWrite(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('dumpFile')
            ->with(self::BASE_PATH . '/foo/bar.txt', 'content');

        $this->distribution->write('foo/bar.txt', 'content');
    }

    public function testWriteException(): void
    {
        $this->filesystem
            ->method('dumpFile')
            ->willThrowException(new \Exception('disk full'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error writing to distribution path');
        $this->distribution->write('file.txt', 'x');
    }

    public function testClearRemovesAllItemsInDirectory(): void
    {
        $removed = [];

        $this->filesystem
            ->expects($this->once())
            ->method('exists')
            ->with(self::BASE_PATH)
            ->willReturn(true);

        $this->filesystem
            ->expects($this->exactly(3))
            ->method('remove')
            ->willReturnCallback(static function (string $path) use (&$removed): void {
                $removed[] = $path;
            });

        $this->distribution->clear();

        $expected = [
            self::BASE_PATH . '/a.txt',
            self::BASE_PATH . '/b.txt',
            self::BASE_PATH . '/sub',
        ];

        sort($removed);
        sort($expected);
        self::assertSame($expected, $removed, 'Unexpected paths removed.');
    }

    public function testClearBaseNotExists(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('exists')
            ->with(self::BASE_PATH)
            ->willReturn(false);

        $this->filesystem
            ->expects($this->never())
            ->method('remove');

        $this->distribution->clear();
    }

    public function testClearBaseNotDir(): void
    {
        $distribution = new FilesystemDistribution(self::BASE_PATH . '/a.txt', $this->filesystem);

        $this->filesystem
            ->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $this->filesystem
            ->expects($this->never())
            ->method('remove');

        $distribution->clear();
    }

    public function testClearException(): void
    {
        $this->filesystem
            ->method('exists')
            ->willReturn(true);

        $this->filesystem
            ->method('remove')
            ->willThrowException(new \Exception('Test error.'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error clearing distribution');
        $this->distribution->clear();
    }

    public function testCopyFile(): void
    {
        $source = self::BASE_PATH . '/a.txt';
        $destination = self::BASE_PATH . '/a_copy.txt';

        $this->filesystem
            ->expects($this->once())
            ->method('copy')
            ->with($source, $destination, true);

        $this->distribution->copy($source, '/a_copy.txt');
    }

    public function testCopyDir(): void
    {
        $source = self::BASE_PATH . '/sub';
        $destination = self::BASE_PATH . '/sub_copy';

        $this->filesystem
            ->expects($this->once())
            ->method('mirror')
            ->with($source, $destination);

        $this->distribution->copy($source, '/sub_copy');
    }

    public function testCopyMissingSource(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/Source path .+ does not exist/');
        $this->distribution->copy(self::BASE_PATH . '/missing.txt', '/something.txt');
    }

    public function testCopyException(): void
    {
        $this->filesystem
            ->method('copy')
            ->willThrowException(new \Exception('Test error.'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error copying file');
        $this->distribution->copy(self::BASE_PATH . '/a.txt', '/a_copy.txt');
    }

    public function testLink(): void
    {
        $source = self::BASE_PATH . '/a.txt';
        $destination = self::BASE_PATH . '/a_link.txt';

        $this->filesystem
            ->expects($this->once())
            ->method('symlink')
            ->with($source, $destination, false);

        $this->distribution->link($source, '/a_link.txt');
    }

    public function testLinkMissingSource(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessageMatches('/Source path .+ does not exist/');
        $this->distribution->link(self::BASE_PATH . '/missing.txt', '/something.txt');
    }

    public function testLinkException(): void
    {
        $this->filesystem
            ->method('symlink')
            ->willThrowException(new \Exception('Test error.'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Error creating symlink');
        $this->distribution->link(self::BASE_PATH . '/a.txt', '/a_link.txt');
    }
}
