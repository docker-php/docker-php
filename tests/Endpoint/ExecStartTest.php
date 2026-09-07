<?php

declare(strict_types=1);

namespace Docker\Tests\Endpoint;

use Docker\API\Endpoint\ExecStart as RawExecStart;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Docker;
use Docker\Endpoint\ExecStart;
use Docker\Stream\DockerRawStream;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ExecStartTest extends TestCase
{
    public static function responseProvider(): iterable
    {
        $framed = pack('CxxxN', 1, 6)."hello\n".pack('CxxxN', 2, 6)."error\n";
        yield 'legacy non-TTY' => [200, DockerRawStream::HEADER, false, $framed, "hello\n", "error\n"];
        yield 'legacy upgraded non-TTY' => [101, DockerRawStream::HEADER, false, $framed, "hello\n", "error\n"];
        yield 'multiplexed non-TTY' => [200, DockerRawStream::MULTIPLEXED_HEADER, false, $framed, "hello\n", "error\n"];
        yield 'upgraded multiplexed non-TTY' => [101, DockerRawStream::MULTIPLEXED_HEADER, false, $framed, "hello\n", "error\n"];
        yield 'media type case and parameters' => [101, 'Application/Vnd.Docker.Multiplexed-Stream; charset=binary', false, $framed, "hello\n", "error\n"];
        yield 'TTY' => [200, DockerRawStream::HEADER, true, "hello\n", "hello\n", ''];
        yield 'upgraded TTY' => [101, DockerRawStream::HEADER, true, "hello\n", "hello\n", ''];
        yield 'TTY media type parameters' => [200, DockerRawStream::HEADER.'; charset=utf-8', true, "hello\n", "hello\n", ''];
        yield 'TTY bytes resembling frames' => [200, DockerRawStream::HEADER, true, $framed, $framed, ''];
        yield 'empty non-TTY' => [200, DockerRawStream::HEADER, false, '', '', ''];
        yield 'empty TTY' => [101, DockerRawStream::HEADER, true, '', '', ''];
        yield 'zero-length frame' => [101, DockerRawStream::MULTIPLEXED_HEADER, false, pack('CxxxN', 1, 0).$framed, "hello\n", "error\n"];
        yield 'omitted TTY defaults to framed' => [200, DockerRawStream::HEADER, null, $framed, "hello\n", "error\n"];
    }

    /** @dataProvider responseProvider */
    public function testReadsExecResponse(int $status, string $contentType, ?bool $tty, string $body, string $expectedStdout, string $expectedStderr): void
    {
        $config = new ExecIdStartPostBody();
        $config->setDetach(false);
        if (null !== $tty) {
            $config->setTty($tty);
        }
        $endpoint = new ExecStart('test', $config);
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->expects(self::never())->method('deserialize');
        $response = new Response($status, ['Content-Type' => $contentType], $body);
        $stream = $endpoint->parseResponse($response, $serializer);

        self::assertInstanceOf(DockerRawStream::class, $stream);
        self::assertSame(0, $response->getBody()->tell(), 'Wrapping must not consume the output');
        $stdout = '';
        $stderr = '';
        $stream->onStdout(static function (string $chunk) use (&$stdout): void {
            $stdout .= $chunk;
        });
        $stream->onStderr(static function (string $chunk) use (&$stderr): void {
            $stderr .= $chunk;
        });
        $stream->wait();

        self::assertSame($expectedStdout, $stdout);
        self::assertSame($expectedStderr, $stderr);
    }

    public function testMissingRequestBodyKeepsFramedDefault(): void
    {
        $stream = (new ExecStart('test'))->parseResponse(
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

    public static function nonStreamProvider(): iterable
    {
        yield 'detached' => [200, '', ''];
        yield 'not found' => [404, 'application/json', '{"message":"No such exec"}'];
        yield 'conflict' => [409, 'application/json', '{"message":"Container is not running"}'];
    }

    /** @dataProvider nonStreamProvider */
    public function testNonStreamResponsesKeepGeneratedBehavior(int $status, string $contentType, string $body): void
    {
        $config = (new ExecIdStartPostBody())->setDetach(true);
        $serializer = $this->createMock(SerializerInterface::class);
        $headers = '' === $contentType ? [] : ['Content-Type' => $contentType];
        $expected = (new RawExecStart('test', $config))->parseResponse(new Response($status, $headers, $body), $serializer);

        self::assertSame($expected, (new ExecStart('test', $config))->parseResponse(new Response($status, $headers, $body), $serializer));
    }

    public function testRawEndpointPreservesFrameHeaders(): void
    {
        $body = pack('CxxxN', 1, 6)."hello\n";
        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects(self::exactly(2))->method('sendRequest')->willReturnOnConsecutiveCalls(
            new Response(200, ['Content-Type' => 'application/json'], '{}'),
            new Response(101, ['Content-Type' => DockerRawStream::MULTIPLEXED_HEADER], $body)
        );
        $docker = Docker::create($httpClient, [], [], false);
        $response = $docker->executeRawEndpoint(new ExecStart('test', (new ExecIdStartPostBody())->setTty(false)));

        self::assertSame(101, $response->getStatusCode());
        self::assertSame($body, $response->getBody()->getContents());
    }
}
