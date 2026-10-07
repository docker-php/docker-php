<?php

declare(strict_types=1);

namespace Docker\Tests\Stream;

use Docker\Stream\SocketReadStream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class SocketReadStreamTest extends TestCase
{
    public static function readSizes(): iterable
    {
        yield 'buffered output' => [7, 8192, 7];
        yield 'buffer exceeds request' => [20, 8, 8];
        yield 'no buffered bytes' => [0, 8192, 8192];
        yield 'metadata unavailable' => [null, 8192, 8192];
        yield 'zero length' => [7, 0, 0];
    }

    #[DataProvider('readSizes')]
    public function testUsesBufferedBytesBeforeReadingMore(?int $buffered, int $requested, int $expectedRead): void
    {
        $source = $this->createMock(StreamInterface::class);
        $source->expects(self::once())->method('getMetadata')->with('unread_bytes')->willReturn($buffered);
        $expected = str_repeat('x', $expectedRead);
        $source->expects(self::once())->method('read')->with($expectedRead)->willReturn($expected);

        self::assertSame($expected, (new SocketReadStream($source))->read($requested));
    }
}
