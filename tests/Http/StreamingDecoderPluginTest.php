<?php

declare(strict_types=1);

namespace Docker\Tests\Http;

use Docker\Http\StreamingDecoderPlugin;
use Docker\Stream\DechunkStream;
use Http\Promise\FulfilledPromise;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class StreamingDecoderPluginTest extends TestCase
{
    private function decode(ResponseInterface $response, array $config = []): ResponseInterface
    {
        $next = static fn () => new FulfilledPromise($response);

        return (new StreamingDecoderPlugin($config))->handleRequest(new Request('GET', 'http://localhost/_ping'), $next, $next)->wait();
    }

    public static function encodings(): iterable
    {
        yield 'chunked' => ['chunked'];
        yield 'case insensitive' => ['CHUNKED'];
        yield 'whitespace' => [' chunked '];
    }

    #[DataProvider('encodings')]
    public function testChunkedResponseUsesIncrementalDecoder(string $encoding): void
    {
        $response = $this->decode(new Response(200, ['Transfer-Encoding' => $encoding], "5\r\nhello\r\n0\r\n\r\n"));

        self::assertInstanceOf(DechunkStream::class, $response->getBody());
        self::assertFalse($response->hasHeader('Transfer-Encoding'));
        self::assertSame('hello', $response->getBody()->getContents());
    }

    public static function unencodedResponses(): iterable
    {
        yield 'ordinary JSON' => [200, ['Content-Type' => 'application/json'], '{"ok":true}'];
        yield 'upgraded stream' => [101, ['Connection' => 'Upgrade', 'Upgrade' => 'tcp'], "\x00raw\r\n"];
        yield 'unknown transfer encoding' => [200, ['Transfer-Encoding' => 'custom'], 'unchanged'];
    }

    #[DataProvider('unencodedResponses')]
    public function testUnencodedResponseIsNotChanged(int $status, array $headers, string $body): void
    {
        $response = new Response($status, $headers, $body);

        $decoded = $this->decode($response);
        self::assertSame($response->getStatusCode(), $decoded->getStatusCode());
        self::assertSame($response->getHeaders(), $decoded->getHeaders());
        self::assertSame($response->getBody(), $decoded->getBody());
        self::assertSame($body, $response->getBody()->getContents());
    }

    public static function compression(): iterable
    {
        yield 'gzip' => ['gzip', false];
        yield 'chunked gzip' => ['gzip', true];
        yield 'deflate' => ['deflate', false];
        yield 'chunked deflate' => ['deflate', true];
    }

    #[DataProvider('compression')]
    public function testRetainsContentDecoding(string $encoding, bool $chunked): void
    {
        $body = 'compressed response';
        $encoded = 'gzip' === $encoding ? gzencode($body) : gzcompress($body);
        $headers = ['Content-Encoding' => $encoding];
        if ($chunked) {
            $encoded = dechex(\strlen($encoded))."\r\n".$encoded."\r\n0\r\n\r\n";
            $headers['Transfer-Encoding'] = 'chunked';
        }
        $response = $this->decode(new Response(200, $headers, $encoded));

        self::assertSame($body, $response->getBody()->getContents());
        self::assertFalse($response->hasHeader('Content-Encoding'));
        self::assertFalse($response->hasHeader('Transfer-Encoding'));
    }

    public function testContentDecodingCanStillBeDisabled(): void
    {
        $encoded = gzencode('compressed response');
        $response = $this->decode(new Response(200, ['Content-Encoding' => 'gzip', 'Transfer-Encoding' => 'chunked'], dechex(\strlen($encoded))."\r\n".$encoded."\r\n0\r\n\r\n"), ['use_content_encoding' => false]);

        self::assertSame($encoded, $response->getBody()->getContents());
        self::assertSame('gzip', $response->getHeaderLine('Content-Encoding'));
        self::assertFalse($response->hasHeader('Transfer-Encoding'));
    }

    public function testPreservesRequestEncodingNegotiation(): void
    {
        $next = static function (RequestInterface $request): FulfilledPromise {
            self::assertSame(['gzip', 'deflate'], $request->getHeader('Accept-Encoding'));
            self::assertSame(['gzip', 'deflate', 'chunked'], $request->getHeader('TE'));

            return new FulfilledPromise(new Response());
        };

        (new StreamingDecoderPlugin())->handleRequest(new Request('GET', 'http://localhost/_ping'), $next, $next)->wait();
    }
}
