<?php

declare(strict_types=1);

namespace Docker\Tests\Stream;

use Docker\Stream\DockerRawStream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class DockerRawStreamTest extends TestCase
{
    public static function streamProvider(): iterable
    {
        $frames = self::frame(1, "hello\n").self::frame(2, "error\n").self::frame(1, "again\n");
        yield 'framed output' => [$frames, true, 8192, "hello\nagain\n", "error\n"];
        yield 'one byte per read' => [$frames, true, 1, "hello\nagain\n", "error\n"];
        yield 'split headers and payloads' => [$frames, true, 7, "hello\nagain\n", "error\n"];
        yield 'empty framed stream' => ['', true, 8192, '', ''];
        yield 'zero-length frame' => [self::frame(1, '').self::frame(1, 'hello'), true, 8192, 'hello', ''];
        yield 'short TTY output' => ["hello\n", false, 8192, "hello\n", ''];
        yield 'TTY output across reads' => ["terminal output\r\n", false, 2, "terminal output\r\n", ''];
        yield 'empty TTY stream' => ['', false, 8192, '', ''];
        yield 'TTY bytes resembling frames' => [$frames, false, 3, $frames, ''];
    }

    /** @dataProvider streamProvider */
    public function testReadsNonSeekableStream(string $body, bool $multiplexed, int $chunkSize, string $expectedStdout, string $expectedStderr): void
    {
        $position = 0;
        $source = $this->createMock(StreamInterface::class);
        $source->method('eof')->willReturnCallback(static function () use (&$position, $body): bool {
            return $position >= \strlen($body);
        });
        $source->method('read')->willReturnCallback(static function (int $length) use (&$position, $body, $chunkSize): string {
            if ($length <= 0) {
                throw new \InvalidArgumentException('Read length must be positive');
            }
            $chunk = substr($body, $position, min($length, $chunkSize));
            $position += \strlen($chunk);

            return $chunk;
        });
        $source->expects(self::never())->method('seek');
        $source->expects(self::never())->method('rewind');
        $source->expects(self::never())->method('getContents');

        $stream = new DockerRawStream($source, $multiplexed);
        $stdout = '';
        $stderr = '';
        $stream->onStdout(static function (string $output) use (&$stdout): void {
            $stdout .= $output;
        });
        $stream->onStderr(static function (string $output) use (&$stderr): void {
            $stderr .= $output;
        });
        $stream->wait();

        self::assertSame($expectedStdout, $stdout);
        self::assertSame($expectedStderr, $stderr);
    }

    private static function frame(int $type, string $output): string
    {
        return pack('CxxxN', $type, \strlen($output)).$output;
    }
}
