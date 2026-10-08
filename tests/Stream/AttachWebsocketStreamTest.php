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
}
