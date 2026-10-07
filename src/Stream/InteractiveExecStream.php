<?php

declare(strict_types=1);

namespace Docker\Stream;

use Docker\Exception\InteractiveExecTimeoutException;

/**
 * An owned, nonblocking Docker exec connection. No output is retained.
 *
 * Closing this connection does not promise to terminate the container process.
 */
final class InteractiveExecStream
{
    /** @var resource|null */
    private $socket;
    private bool $stdinClosed = false;
    private bool $finished = false;
    private string $header = '';
    private int $remaining = 0;
    private int $channel = 1;
    private ?int $deadline;
    /** @var callable[] */
    private array $stdout = [];
    /** @var callable[] */
    private array $stderr = [];

    /**
     * @param resource $socket    An upgraded socket, including its PHP read buffer
     * @param ?int     $timeoutMs Total streaming deadline after the HTTP upgrade
     */
    public function __construct($socket, private bool $multiplexed = true, ?int $timeoutMs = null)
    {
        if (!\is_resource($socket) || 'stream' !== get_resource_type($socket)) {
            throw new \InvalidArgumentException('An interactive exec socket is required.');
        }
        if (null !== $timeoutMs && ($timeoutMs < 1 || $timeoutMs > 86400000)) {
            throw new \InvalidArgumentException('Interactive exec timeout must be between 1 and 86400000 milliseconds.');
        }
        if (!stream_set_blocking($socket, false)) {
            throw new \RuntimeException('Cannot make the interactive exec socket nonblocking.');
        }
        $this->socket = $socket;
        $this->deadline = null === $timeoutMs ? null : hrtime(true) + $timeoutMs * 1000000;
    }

    public function onStdout(callable $callback): void
    {
        $this->stdout[] = $callback;
    }

    public function onStderr(callable $callback): void
    {
        $this->stderr[] = $callback;
    }

    /**
     * Attempt a bounded write. Retry the unsent suffix after polling.
     * Returns zero on backpressure; it does not queue or retain stdin.
     */
    public function writeStdin(string $data): int
    {
        $this->checkDeadline();
        $this->requireOpen();
        if ($this->stdinClosed) {
            throw new \LogicException('Interactive exec stdin is closed.');
        }
        if ('' === $data) {
            return 0;
        }
        $written = @fwrite($this->socket, substr($data, 0, 16384));
        if (false === $written) {
            $this->close();
            throw new \RuntimeException('Cannot write interactive exec stdin.');
        }

        return $written;
    }

    /** Send EOF to stdin, while keeping stdout and stderr readable. */
    public function closeStdin(): void
    {
        $this->checkDeadline();
        $this->requireOpen();
        if (!$this->multiplexed) {
            throw new \LogicException('TTY stdin cannot be half-closed safely; use terminal input or wait for the command to exit.');
        }
        if (!$this->stdinClosed) {
            if (!@stream_socket_shutdown($this->socket, \STREAM_SHUT_WR)) {
                $this->close();
                throw new \RuntimeException('Cannot close interactive exec stdin.');
            }
            $this->stdinClosed = true;
        }
    }

    /**
     * Read at most 16 KiB, waiting at most timeoutMs for data. True means the
     * session remains open (possibly idle); false means a clean output EOF.
     * Callbacks receive chunks, not necessarily whole Docker frames or lines.
     */
    public function poll(int $timeoutMs = 0): bool
    {
        if ($timeoutMs < 0 || $timeoutMs > 60000) {
            throw new \InvalidArgumentException('Poll timeout must be between 0 and 60000 milliseconds.');
        }
        if ($this->finished) {
            return false;
        }
        $this->checkDeadline();
        $this->requireOpen();

        try {
            // Try the nonblocking read first: TLS and PHP may already have
            // buffered bytes even when the underlying descriptor is not ready.
            $data = @fread($this->socket, 16384);
            if (false === $data) {
                throw new \RuntimeException('Cannot read interactive exec output.');
            }
            if ('' === $data && !feof($this->socket)) {
                $waitNs = $timeoutMs * 1000000;
                if (null !== $this->deadline) {
                    $waitNs = min($waitNs, max(0, $this->deadline - hrtime(true)));
                }
                $read = [$this->socket];
                $write = $except = [];
                $selected = @stream_select($read, $write, $except, intdiv($waitNs, 1000000000), intdiv($waitNs % 1000000000, 1000));
                $this->checkDeadline();
                if (false === $selected) {
                    throw new \RuntimeException('Cannot poll interactive exec output.');
                }
                if (0 === $selected) {
                    return true;
                }
                $data = @fread($this->socket, 16384);
                if (false === $data) {
                    throw new \RuntimeException('Cannot read interactive exec output.');
                }
            }
            $this->checkDeadline();
            if ('' !== $data) {
                $this->dispatch($data);
            }
            $this->requireOpen();
            if (feof($this->socket)) {
                if ('' !== $this->header || $this->remaining > 0) {
                    throw new \RuntimeException('Docker closed an incomplete exec frame.');
                }
                $this->finished = true;
                $this->close();
            }

            return !$this->finished;
        } catch (\Throwable $error) {
            $this->close();
            throw $error;
        }
    }

    /** The tick runs even when the command produces no output. */
    public function wait(?callable $tick = null, int $pollIntervalMs = 100): void
    {
        try {
            while (!$this->finished) {
                if (null !== $tick) {
                    $tick();
                }
                $this->poll($pollIntervalMs);
            }
        } catch (\Throwable $error) {
            $this->close();
            throw $error;
        }
    }

    public function isFinished(): bool
    {
        return $this->finished;
    }

    /** Cancel local I/O only; the in-container process may still be running. */
    public function close(): void
    {
        if (\is_resource($this->socket)) {
            fclose($this->socket);
        }
        $this->socket = null;
        $this->header = '';
        $this->stdout = $this->stderr = [];
    }

    public function __destruct()
    {
        $this->close();
    }

    private function requireOpen(): void
    {
        if (!\is_resource($this->socket)) {
            throw new \LogicException('Interactive exec session is closed.');
        }
    }

    private function checkDeadline(): void
    {
        if (null !== $this->deadline && hrtime(true) >= $this->deadline) {
            $this->close();
            throw new InteractiveExecTimeoutException('Interactive exec exceeded its streaming deadline.');
        }
    }

    private function dispatch(string $data): void
    {
        if (!$this->multiplexed) {
            foreach ($this->stdout as $callback) {
                $callback($data);
            }

            return;
        }
        $offset = 0;
        $length = \strlen($data);
        while ($offset < $length) {
            if (0 === $this->remaining) {
                $take = min(8 - \strlen($this->header), $length - $offset);
                $this->header .= substr($data, $offset, $take);
                $offset += $take;
                if (8 !== \strlen($this->header)) {
                    return;
                }
                $frame = unpack('Cchannel/C3padding/Nsize', $this->header);
                if ($frame['channel'] > 2 || "\0\0\0" !== substr($this->header, 1, 3)) {
                    throw new \RuntimeException('Docker returned an invalid exec frame.');
                }
                $this->channel = $frame['channel'];
                $this->remaining = $frame['size'];
                $this->header = '';
                if (0 === $this->remaining) {
                    continue;
                }
            }
            $take = min($this->remaining, $length - $offset);
            if (0 === $take) {
                return;
            }
            $output = substr($data, $offset, $take);
            $offset += $take;
            $this->remaining -= $take;
            foreach (2 === $this->channel ? $this->stderr : $this->stdout as $callback) {
                $callback($output);
            }
        }
    }
}
