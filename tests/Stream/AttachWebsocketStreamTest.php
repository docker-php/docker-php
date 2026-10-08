<?php

declare(strict_types=1);

namespace Docker\Tests\Stream;

use Docker\Stream\AttachWebsocketStream;
use Nyholm\Psr7\Stream;
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
