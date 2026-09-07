<?php

declare(strict_types=1);

namespace Docker\Tests\Endpoint;

use Docker\API\Exception\ContainerLogsNotFoundException;
use Docker\Endpoint\ContainerLogs;
use Docker\Stream\DockerRawStream;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ContainerLogsTest extends TestCase
{
    public static function responseProvider(): iterable
    {
        $framed = pack('CxxxN', 1, 6)."hello\n";
        yield 'modern multiplexed stream' => [DockerRawStream::MULTIPLEXED_HEADER, $framed, false, 0];
        yield 'media type case and parameters' => ['Application/Vnd.Docker.Multiplexed-Stream; charset=binary', $framed, false, 0];
        yield 'legacy framed raw stream' => [DockerRawStream::HEADER, $framed, false, 1];
        yield 'TTY raw stream' => [DockerRawStream::HEADER, "hello\n", true, 1];
        yield 'TTY media type with parameters' => [DockerRawStream::HEADER.'; charset=utf-8', "hello\n", true, 1];
    }

    /** @dataProvider responseProvider */
    public function testReadsLogResponse(string $contentType, string $body, bool $tty, int $expectedInspections): void
    {
        $inspections = 0;
        $endpoint = new ContainerLogs('test', ['stdout' => true], [], static function () use (&$inspections, $tty): bool {
            ++$inspections;

            return $tty;
        });
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects(self::never())->method('deserialize');
        $response = new Response(200, ['Content-Type' => $contentType], $body);
        $stream = $endpoint->parseResponse($response, $serializer);

        self::assertInstanceOf(DockerRawStream::class, $stream);
        self::assertSame(0, $response->getBody()->tell(), 'Creating the wrapper must not consume the logs');
        $output = '';
        $stream->onStdout(static function (string $chunk) use (&$output): void {
            $output .= $chunk;
        });
        $stream->wait();

        self::assertSame("hello\n", $output);
        self::assertSame($expectedInspections, $inspections);
    }

    public function testDirectEndpointKeepsLegacyFramedBehavior(): void
    {
        $endpoint = new ContainerLogs('test', ['stdout' => true]);
        $stream = $endpoint->parseResponse(
            new Response(200, ['Content-Type' => DockerRawStream::HEADER], pack('CxxxN', 1, 5).'hello'),
            $this->createMock(SerializerInterface::class)
        );
        $output = '';
        $stream->onStdout(static function (string $chunk) use (&$output): void {
            $output .= $chunk;
        });
        $stream->wait();

        self::assertSame('hello', $output);
    }

    public function testNotFoundResponseUsesGeneratedException(): void
    {
        $endpoint = new ContainerLogs('missing', ['stdout' => true], [], static function (): bool {
            throw new \LogicException('An error response must not inspect the container');
        });

        $this->expectException(ContainerLogsNotFoundException::class);

        $endpoint->parseResponse(
            new Response(404, ['Content-Type' => 'application/json'], '{"message":"No such container: missing"}'),
            $this->createMock(SerializerInterface::class)
        );
    }
}
