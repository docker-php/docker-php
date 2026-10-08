<?php

declare(strict_types=1);

namespace Docker\Tests\Endpoint;

use Docker\Endpoint\ContainerAttach;
use Docker\Endpoint\ContainerAttachWebsocket;
use Docker\Stream\AttachWebsocketStream;
use Docker\Stream\DockerRawStream;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ContainerAttachTest extends TestCase
{
    public static function responseProvider(): iterable
    {
        $framed = pack('CxxxN', 1, 6)."hello\n".pack('CxxxN', 2, 5)."oops\n";
        yield 'non-TTY plain response' => [200, DockerRawStream::HEADER, false, $framed, "hello\n", "oops\n", 1];
        yield 'TTY plain response' => [200, DockerRawStream::HEADER, true, "hello\n", "hello\n", '', 1];
        yield 'non-TTY upgraded response' => [101, DockerRawStream::MULTIPLEXED_HEADER, false, $framed, "hello\n", "oops\n", 0];
        yield 'TTY upgraded response' => [101, DockerRawStream::HEADER, true, "hello\n", "hello\n", '', 1];
        yield 'multiplexed plain response' => [200, DockerRawStream::MULTIPLEXED_HEADER.'; charset=binary', false, $framed, "hello\n", "oops\n", 0];
    }

    #[DataProvider('responseProvider')]
    public function testReadsAttachResponse(int $status, string $contentType, bool $tty, string $body, string $stdout, string $stderr, int $inspections): void
    {
        $calls = 0;
        $endpoint = new ContainerAttach('web', ['stream' => true], [], static function () use (&$calls, $tty): bool {
            ++$calls;

            return $tty;
        });
        $stream = $endpoint->parseResponse(new Response($status, ['Content-Type' => $contentType], $body), $this->createMock(SerializerInterface::class));

        $this->assertInstanceOf(DockerRawStream::class, $stream);
        $output = ['stdout' => '', 'stderr' => ''];
        $stream->onStdout(static function (string $chunk) use (&$output): void {
            $output['stdout'] .= $chunk;
        });
        $stream->onStderr(static function (string $chunk) use (&$output): void {
            $output['stderr'] .= $chunk;
        });
        $stream->wait();

        $this->assertSame(['stdout' => $stdout, 'stderr' => $stderr], $output);
        $this->assertSame($inspections, $calls);
    }

    public function testWithoutResolverKeepsFramedBehavior(): void
    {
        $stream = (new ContainerAttach('web'))->parseResponse(
            new Response(200, ['Content-Type' => DockerRawStream::HEADER], pack('CxxxN', 1, 5).'hello'),
            $this->createMock(SerializerInterface::class)
        );
        $output = '';
        $stream->onStdout(static function (string $chunk) use (&$output): void {
            $output .= $chunk;
        });
        $stream->wait();

        $this->assertSame('hello', $output);
    }

    public function testWebsocketUpgradeReturnsWebsocketStream(): void
    {
        $socket = fopen('php://temp', 'w+');
        $stream = (new ContainerAttachWebsocket('web'))->parseResponse(
            new Response(101, ['Upgrade' => 'WebSocket', 'Connection' => 'Upgrade'], $socket),
            $this->createMock(SerializerInterface::class)
        );

        $this->assertInstanceOf(AttachWebsocketStream::class, $stream);
    }

    public function testWebsocketWithoutUpgradeIsNotWrapped(): void
    {
        $stream = (new ContainerAttachWebsocket('web'))->parseResponse(
            new Response(200, ['Content-Type' => DockerRawStream::HEADER], 'hello'),
            $this->createMock(SerializerInterface::class)
        );

        $this->assertNotInstanceOf(AttachWebsocketStream::class, $stream);
    }

    public function testWebsocketHandshakeKeyIsSixteenRandomBytes(): void
    {
        $key = (new ContainerAttachWebsocket('web'))->getExtraHeaders()['Sec-WebSocket-Key'];

        $this->assertSame(16, \strlen(base64_decode($key, true)));
        $this->assertNotSame($key, (new ContainerAttachWebsocket('web'))->getExtraHeaders()['Sec-WebSocket-Key']);
    }
}
