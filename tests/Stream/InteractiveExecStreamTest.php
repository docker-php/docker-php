<?php

declare(strict_types=1);

namespace Docker\Tests\Stream;

use Docker\Exception\InteractiveExecTimeoutException;
use Docker\Stream\InteractiveExecStream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class InteractiveExecStreamTest extends TestCase
{
    private array $sockets = [];
    private ?InteractiveExecStream $stream = null;

    protected function tearDown(): void
    {
        $this->stream?->close();
        foreach ($this->sockets as $socket) {
            if (\is_resource($socket)) {
                fclose($socket);
            }
        }
        parent::tearDown();
    }

    private function open(bool $multiplexed = true, ?int $timeoutMs = null): InteractiveExecStream
    {
        $this->sockets = stream_socket_pair(\STREAM_PF_UNIX, \STREAM_SOCK_STREAM, \STREAM_IPPROTO_IP);
        self::assertIsArray($this->sockets);
        stream_set_blocking($this->sockets[1], false);

        return $this->stream = new InteractiveExecStream($this->sockets[0], $multiplexed, $timeoutMs);
    }

    public function testHalfCloseKeepsOutputReadable(): void
    {
        $stream = $this->open();
        self::assertSame(5, $stream->writeStdin('input'));
        self::assertSame('input', fread($this->sockets[1], 20));
        $stream->closeStdin();
        $stream->closeStdin();
        self::assertSame('', fread($this->sockets[1], 20));
        self::assertTrue(feof($this->sockets[1]));
        fwrite($this->sockets[1], pack('CxxxN', 1, 6).'result');
        fclose($this->sockets[1]);
        $output = '';
        $stream->onStdout(static function (string $chunk) use (&$output): void { $output .= $chunk; });
        $stream->wait();
        self::assertSame('result', $output);
        self::assertTrue($stream->isFinished());
        self::assertFalse($stream->poll());
    }

    public function testPartialWritesAndBackpressureAreBounded(): void
    {
        $stream = $this->open();
        $payload = str_repeat('x', 1048576);
        $offset = 0;
        $started = hrtime(true);
        do {
            $written = $stream->writeStdin(substr($payload, $offset));
            self::assertLessThanOrEqual(16384, $written);
            $offset += $written;
        } while ($written > 0 && $offset < \strlen($payload));
        self::assertSame(0, $written, 'The peer must eventually apply backpressure.');
        self::assertLessThan(500000000, hrtime(true) - $started, 'writeStdin blocked.');
        self::assertLessThan(\strlen($payload), $offset);
        $received = '';
        while ($offset < \strlen($payload)) {
            $received .= stream_get_contents($this->sockets[1]);
            $offset += $stream->writeStdin(substr($payload, $offset));
        }
        $received .= stream_get_contents($this->sockets[1]);
        self::assertSame($payload, $received);
    }

    public function testFragmentedHeadersPayloadsAndEmptyFrames(): void
    {
        $stream = $this->open();
        $stdout = $stderr = '';
        $stream->onStdout(static function (string $chunk) use (&$stdout): void { $stdout .= $chunk; });
        $stream->onStderr(static function (string $chunk) use (&$stderr): void { $stderr .= $chunk; });
        $wire = pack('CxxxN', 1, 0).pack('CxxxN', 1, 5).'hello'.pack('CxxxN', 2, 5).'error'.pack('CxxxN', 0, 4).'echo';
        foreach (str_split($wire, 3) as $chunk) {
            fwrite($this->sockets[1], $chunk);
            self::assertTrue($stream->poll());
        }
        fclose($this->sockets[1]);
        $stream->wait();
        self::assertSame('helloecho', $stdout);
        self::assertSame('error', $stderr);
    }

    public function testTtyBytesRemainUnchanged(): void
    {
        $stream = $this->open(false);
        $output = '';
        $stream->onStdout(static function (string $chunk) use (&$output): void { $output .= $chunk; });
        $stream->onStderr(static function (): void { self::fail('TTY output has no separate stderr.'); });
        $bytes = "\0\xffhello\r\n";
        fwrite($this->sockets[1], $bytes);
        fclose($this->sockets[1]);
        $stream->wait();
        self::assertSame($bytes, $output);
    }

    public function testIdlePollIsBoundedAndTicksReachDeadline(): void
    {
        $stream = $this->open(true, 160);
        $started = hrtime(true);
        self::assertTrue($stream->poll(30));
        self::assertGreaterThan(20000000, hrtime(true) - $started);
        $ticks = 0;
        try {
            $stream->wait(static function () use (&$ticks): void { ++$ticks; }, 30);
            self::fail('An idle session exceeded its deadline.');
        } catch (InteractiveExecTimeoutException $expected) {
            self::assertGreaterThanOrEqual(3, $ticks);
            self::assertLessThan(500000000, hrtime(true) - $started);
            self::assertFalse($stream->isFinished(), 'A timeout is not successful completion.');
        }
    }

    public static function invalidFrames(): array
    {
        return [
            'partial header' => ["\x01\x00"],
            'partial payload' => [pack('CxxxN', 1, 5).'ab'],
            'invalid channel' => [pack('CxxxN', 9, 0)],
            'invalid padding' => ["\x01\x01\x00\x00\x00\x00\x00\x00"],
            'system error frame' => [pack('CxxxN', 3, 0)],
        ];
    }

    #[DataProvider('invalidFrames')]
    public function testInvalidOrTruncatedFramesFail(string $wire): void
    {
        $stream = $this->open();
        fwrite($this->sockets[1], $wire);
        fclose($this->sockets[1]);
        $this->expectException(\RuntimeException::class);
        $stream->wait();
    }

    public function testHugeFrameDoesNotAllocateItsDeclaredSize(): void
    {
        $stream = $this->open();
        $before = memory_get_usage(true);
        fwrite($this->sockets[1], pack('CxxxN', 1, 4294967295).'x');
        self::assertTrue($stream->poll());
        self::assertLessThan(1048576, memory_get_usage(true) - $before);
    }

    public function testCallbackFailureClosesConnection(): void
    {
        $stream = $this->open();
        $error = new \RuntimeException('callback failed');
        $stream->onStdout(static function () use ($error): void { throw $error; });
        fwrite($this->sockets[1], pack('CxxxN', 1, 1).'x');
        try {
            $stream->poll();
            self::fail('Callback error was swallowed.');
        } catch (\RuntimeException $caught) {
            self::assertSame($error, $caught);
            self::assertSame('', fread($this->sockets[1], 1));
            self::assertTrue(feof($this->sockets[1]));
        }
    }

    public function testHeartbeatFailureClosesConnection(): void
    {
        $stream = $this->open();
        $this->expectExceptionMessage('lock lost');
        try {
            $stream->wait(static function (): void { throw new \RuntimeException('lock lost'); });
        } finally {
            self::assertSame('', fread($this->sockets[1], 1));
            self::assertTrue(feof($this->sockets[1]));
        }
    }

    public function testWriteAfterHalfCloseFails(): void
    {
        $stream = $this->open();
        $stream->closeStdin();
        $this->expectException(\LogicException::class);
        $stream->writeStdin('x');
    }

    public function testTtyHalfCloseIsRejectedWithoutLosingTheConnection(): void
    {
        $stream = $this->open(false);
        try {
            $stream->closeStdin();
            self::fail('Unsafe TTY half-close was accepted.');
        } catch (\LogicException $expected) {
            self::assertSame(1, $stream->writeStdin('x'));
        }
    }

    public function testExplicitCloseIsNotSuccessfulEof(): void
    {
        $stream = $this->open();
        $stream->close();
        $stream->close();
        self::assertFalse($stream->isFinished());
        $this->expectException(\LogicException::class);
        $stream->poll();
    }

    public function testWriteFailureClosesConnection(): void
    {
        $stream = $this->open();
        fclose($this->sockets[1]);
        $this->expectException(\RuntimeException::class);
        $stream->writeStdin('x');
    }

    public function testInvalidPollTimeoutIsRejected(): void
    {
        $stream = $this->open();
        $this->expectException(\InvalidArgumentException::class);
        $stream->poll(60001);
    }
}
