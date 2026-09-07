<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\API\Endpoint\ContainerLogs;
use Docker\Docker;
use Docker\Stream\DockerRawStream;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DockerLogsTest extends TestCase
{
    public static function logsProvider(): iterable
    {
        $framed = pack('CxxxN', 1, 6)."hello\n";
        yield 'modern non-TTY' => [DockerRawStream::MULTIPLEXED_HEADER, $framed, false, ['/containers/test/logs']];
        yield 'modern TTY' => [DockerRawStream::HEADER, "hello\n", true, ['/containers/test/logs', '/containers/test/json']];
        yield 'legacy non-TTY' => [DockerRawStream::HEADER, $framed, false, ['/containers/test/logs', '/containers/test/json']];
    }

    /** @dataProvider logsProvider */
    public function testContainerLogs(string $contentType, string $body, bool $tty, array $expectedPaths): void
    {
        $paths = [];
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('sendRequest')->willReturnCallback(static function (RequestInterface $request) use (&$paths, $contentType, $body, $tty): ResponseInterface {
            $path = $request->getUri()->getPath();
            if ('/info' === $path) {
                return new Response(200, ['Content-Type' => 'application/json'], '{}');
            }
            $paths[] = $path;
            if ('/containers/test/logs' === $path) {
                return new Response(200, ['Content-Type' => $contentType], $body);
            }
            if ('/containers/test/json' === $path) {
                return new Response(200, ['Content-Type' => 'application/json'], json_encode(['Config' => ['Tty' => $tty]], \JSON_THROW_ON_ERROR));
            }

            throw new \LogicException('Unexpected request: '.$path);
        });
        $docker = Docker::create($httpClient, [], [], false);
        $stream = $docker->containerLogs('test', ['stdout' => true]);

        self::assertInstanceOf(DockerRawStream::class, $stream);
        $output = '';
        $stream->onStdout(static function (string $chunk) use (&$output): void {
            $output .= $chunk;
        });
        $stream->wait();

        self::assertSame("hello\n", $output);
        self::assertSame($expectedPaths, $paths);
    }

    public function testRawEndpointStillReturnsUnmodifiedResponse(): void
    {
        $body = pack('CxxxN', 1, 6)."hello\n";
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects(self::exactly(2))->method('sendRequest')->willReturnOnConsecutiveCalls(
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
            new Response(200, ['Content-Type' => DockerRawStream::MULTIPLEXED_HEADER], $body)
        );
        $docker = Docker::create($httpClient, [], [], false);
        $response = $docker->executeRawEndpoint(new ContainerLogs('test', ['stdout' => true]));

        self::assertSame($body, $response->getBody()->getContents());
    }
}
