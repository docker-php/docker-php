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
        $this->assertSame("\x82".$lengthByte.$extendedLength, substr($frame, 0, $header));
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

    public function testAnswersPingAndReturnsNextDataFrame(): void
    {
        [$client, $server] = $this->socketPair();
        fwrite($server, "\x89\x02hi\x82\x02ok");
        $stream = new AttachWebsocketStream(Stream::create($client));

        $this->assertSame('ok', $stream->read());
        $pong = fread($server, 16);
        $this->assertSame("\x8A\x82", substr($pong, 0, 2));
        $this->assertSame('hi', substr($pong, 6) ^ substr($pong, 2, 4));
    }

    public function testCloseFrameRepliesAndEndsStream(): void
    {
        [$client, $server] = $this->socketPair();
        fwrite($server, "\x88\x02\x03\xe8");
        $stream = new AttachWebsocketStream(Stream::create($client));

        $this->assertNull($stream->read());
        $this->assertSame("\x88\x80", substr(fread($server, 16), 0, 2));
        $this->assertNull($stream->read());
    }

    public function testReadsSixtyFourBitLength(): void
    {
        [$client, $server] = $this->socketPair();
        fwrite($server, "\x82\x7F".pack('J', 3).'abc');
        $stream = new AttachWebsocketStream(Stream::create($client));

        $this->assertSame('abc', $stream->read());
    }

    public function testConnectionEndingMidFrameEndsStream(): void
    {
        [$client, $server] = $this->socketPair();
        fwrite($server, "\x82\x05ab");
        fclose($server);
        $stream = new AttachWebsocketStream(Stream::create($client));

        $this->assertNull($stream->read());
        $this->assertNull($stream->read());
    }

    public function testStalledFrameTimesOut(): void
    {
        [$client, $server] = $this->socketPair();
        stream_set_timeout($client, 0, 100000);
        fwrite($server, "\x82\x05ab");
        $stream = new AttachWebsocketStream(Stream::create($client));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Timed out');
        $stream->read();
    }

    /**
     * @return array{resource, resource}
     */
    private function socketPair(): array
    {
        return stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
    }
}
