<?php

declare(strict_types=1);

namespace Docker\Tests\Stream;

use Docker\Stream\AttachWebsocketStream;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AttachWebsocketStreamTest extends TestCase
{
    public function testReadsEmptyFrameBeforePayload(): void
    {
        [$client, $server] = stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
        // An empty text frame followed by an unmasked "hi" text frame.
        fwrite($server, "\x81\x00\x81\x02hi");

        $stream = new AttachWebsocketStream(Stream::create($client));

        $this->assertSame('', $stream->read());
        $this->assertSame('hi', $stream->read());
        fclose($server);
    }

    public static function payloadLengths(): iterable
    {
        yield '7-bit length' => [125, "\xFD", ''];
        yield '16-bit length' => [126, "\xFE", pack('n', 126)];
        yield 'largest 16-bit length' => [0xFFFF, "\xFE", pack('n', 0xFFFF)];
        yield 'smallest 64-bit length' => [0x10000, "\xFF", pack('J', 0x10000)];
    }

    #[DataProvider('payloadLengths')]
    public function testWriteEncodesPayloadLength(int $length, string $lengthByte, string $extendedLength): void
    {
        $socket = fopen('php://temp', 'w+');
        $stream = new AttachWebsocketStream(Stream::create($socket));
        $data = str_repeat('x', $length);

        $stream->write($data);

        rewind($socket);
        $frame = stream_get_contents($socket);
        $header = 2 + \strlen($extendedLength);
        $this->assertSame("\x81".$lengthByte.$extendedLength, substr($frame, 0, $header));
        $mask = substr($frame, $header, 4);
        $payload = substr($frame, $header + 4);
        $this->assertSame($length, \strlen($payload));
        $this->assertSame($data, $payload ^ str_repeat($mask, intdiv($length, 4) + 1));
    }

    public function testCloseSendsCloseFrameAndClosesSocket(): void
    {
        [$client, $server] = stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
        $stream = new AttachWebsocketStream(Stream::create($client));

        $stream->close();
        $stream->close();

        $frame = fread($server, 16);
        $this->assertSame("\x88\x80", substr($frame, 0, 2));
        $this->assertSame(6, \strlen($frame));
        $this->assertSame('', fread($server, 1));
        $this->assertTrue(feof($server));
        $this->assertNull($stream->read());
        fclose($server);
    }

    public function testCloseIgnoresPeerThatAlreadyClosed(): void
    {
        [$client, $server] = stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
        fclose($server);
        $stream = new AttachWebsocketStream(Stream::create($client));

        $stream->close();

        $this->assertNull($stream->read());
    }

    public function testWriteAfterCloseThrows(): void
    {
        [$client, $server] = stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
        $stream = new AttachWebsocketStream(Stream::create($client));
        $stream->close();
        fclose($server);

        $this->expectException(\RuntimeException::class);
        $stream->write('exit');
    }
}
