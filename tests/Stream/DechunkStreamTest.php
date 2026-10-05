<?php

declare(strict_types=1);

namespace Docker\Tests\Stream;

use Docker\Stream\DechunkStream;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class DechunkStreamTest extends TestCase
{
    public function testReturnsAvailableOutputWithoutWaitingForAnotherChunk(): void
    {
        $source = $this->createMock(StreamInterface::class);
        $source->method('eof')->willReturn(false);
        $source->expects(self::once())->method('read')->with(8192)->willReturn("5\r\nhello\r\n");

        self::assertSame('hello', (new DechunkStream($source))->read(8192));
    }

    public static function bodies(): iterable
    {
        yield 'empty body' => ["0\r\n\r\n", '', 8192, 8192];
        yield 'one chunk' => ["5\r\nhello\r\n0\r\n\r\n", 'hello', 8192, 8192];
        yield 'short reads' => ["5\r\nhello\r\n5\r\nworld\r\n0\r\n\r\n", 'helloworld', 8192, 3];
        yield 'split headers and payloads' => ["5\r\nhello\r\n5\r\nworld\r\n0\r\n\r\n", 'helloworld', 1, 8192];
        yield 'chunk extensions and trailers' => ["5;foo=bar\r\nhello\r\n0\r\nX-Test: yes\r\n\r\n", 'hello', 2, 3];
        yield 'binary payload' => ["4\r\n\x00\xff\r\n\r\n0\r\n\r\n", "\x00\xff\r\n", 3, 1];
        $large = str_repeat('abc', 10_000);
        yield 'larger than the read buffer' => [dechex(\strlen($large))."\r\n".$large."\r\n0\r\n\r\n", $large, 8192, 8192];
    }

    #[DataProvider('bodies')]
    public function testDecodesNonSeekableInput(string $body, string $expected, int $sourceReadSize, int $readSize): void
    {
        $position = 0;
        $source = $this->createMock(StreamInterface::class);
        $source->method('eof')->willReturnCallback(static function () use (&$position, $body): bool {
            return $position >= \strlen($body);
        });
        $source->method('read')->willReturnCallback(static function (int $length) use (&$position, $body, $sourceReadSize): string {
            $output = substr($body, $position, min($length, $sourceReadSize));
            $position += \strlen($output);

            return $output;
        });
        $source->expects(self::never())->method('seek');
        $source->expects(self::never())->method('rewind');
        $source->expects(self::never())->method('getContents');
        $stream = new DechunkStream($source);
        $output = '';
        while (!$stream->eof()) {
            $chunk = $stream->read($readSize);
            self::assertLessThanOrEqual($readSize, \strlen($chunk));
            $output .= $chunk;
        }

        self::assertSame($expected, $output);
        self::assertSame('', $stream->read($readSize));
        self::assertFalse($stream->isSeekable());
        self::assertNull($stream->getSize());
    }

    public function testZeroLengthReadDoesNotConsumeInput(): void
    {
        $stream = new DechunkStream(Stream::create("5\r\nhello\r\n0\r\n\r\n"));

        self::assertSame('', $stream->read(0));
        self::assertSame('hello', $stream->getContents());
    }

    public function testNegativeLengthReadIsRejected(): void
    {
        $source = $this->createMock(StreamInterface::class);
        $source->expects(self::never())->method('read');
        $this->expectException(\InvalidArgumentException::class);

        (new DechunkStream($source))->read(-1);
    }
}
