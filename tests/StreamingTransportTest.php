<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\DockerClientFactory;
use Docker\Stream\DockerRawStream;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\DataProvider;

class StreamingTransportTest extends TransportTestCase
{
    public static function streams(): iterable
    {
        foreach (['TCP' => false, 'Unix' => true] as $transport => $unix) {
            foreach (['TTY' => false, 'multiplexed' => true] as $format => $multiplexed) {
                yield $transport.' chunked '.$format => [$unix, $multiplexed, true];
                yield $transport.' upgraded '.$format => [$unix, $multiplexed, false];
            }
        }
    }

    #[DataProvider('streams')]
    public function testCallbacksReceiveOutputBeforeThePeerFinishes(bool $unix, bool $multiplexed, bool $chunked): void
    {
        $first = $multiplexed ? pack('CxxxN', 1, 6)."first\n".pack('CxxxN', 2, 6)."error\n" : "first\r\nerror\r\n";
        $last = $multiplexed ? pack('CxxxN', 1, 5)."last\n".pack('CxxxN', 2, 6)."final\n" : "last\r\nfinal\r\n";
        $response = ['status' => 101, 'headers' => ['Connection' => 'Upgrade', 'Upgrade' => 'tcp']];
        if ($chunked) {
            $response = ['headers' => ['Transfer-Encoding' => 'chunked']];
            $first = dechex(\strlen($first))."\r\n".$first."\r\n";
            $last = dechex(\strlen($last))."\r\n".$last."\r\n0\r\n\r\n";
        }
        $address = $this->startServer(['responses' => [$response + [
            'segments_base64' => [base64_encode($first), base64_encode($last)],
            'delay_ms' => 1000,
        ]]], $unix);
        $client = DockerClientFactory::create(['remote_socket' => ($unix ? 'unix://' : 'tcp://').$address, 'timeout' => 3000]);
        $response = $client->sendRequest(new Request('GET', '/stream'));
        $stream = new DockerRawStream($response->getBody(), $multiplexed);
        $stdout = '';
        $stderr = '';
        $firstCallback = null;
        $lastCallback = null;
        $stream->onStdout(static function (string $output) use (&$stdout, &$firstCallback, &$lastCallback): void {
            $firstCallback ??= hrtime(true);
            $lastCallback = hrtime(true);
            $stdout .= $output;
        });
        $stream->onStderr(static function (string $output) use (&$stderr, &$firstCallback, &$lastCallback): void {
            $firstCallback ??= hrtime(true);
            $lastCallback = hrtime(true);
            $stderr .= $output;
        });
        $stream->wait();

        self::assertSame($multiplexed ? "first\nlast\n" : "first\r\nerror\r\nlast\r\nfinal\r\n", $stdout);
        self::assertSame($multiplexed ? "error\nfinal\n" : '', $stderr);
        self::assertNotNull($firstCallback);
        self::assertGreaterThan(500_000_000, $lastCallback - $firstCallback, 'Output was buffered until the peer finished.');
        self::assertStringContainsString('GET /v1.45/stream HTTP/1.1', $this->serverResult()['request']);
    }
}
