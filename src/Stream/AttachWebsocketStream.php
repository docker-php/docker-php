<?php

declare(strict_types=1);

namespace Docker\Stream;

use Psr\Http\Message\StreamInterface;

/**
 * An interactive stream is used when communicating with an attached docker container.
 *
 * It helps dealing with encoding and decoding frame from websocket protocol (hybi 10)
 *
 * @see https://tools.ietf.org/html/rfc6455#section-5.2
 */
class AttachWebsocketStream
{
    private const OPCODE_BINARY = 0x2;
    private const OPCODE_CLOSE = 0x8;
    private const OPCODE_PING = 0x9;
    private const OPCODE_PONG = 0xA;

    /** @var resource The underlying socket */
    private $socket;

    public function __construct(StreamInterface $stream)
    {
        $this->socket = $stream->detach();
    }

    /**
     * Send input to the container as one binary frame.
     *
     * @param string $data Data to send
     */
    public function write($data): void
    {
        if (!\is_resource($this->socket)) {
            throw new \RuntimeException('The WebSocket stream is closed.');
        }

        $this->sendFrame(self::OPCODE_BINARY, (string) $data);
    }

    /**
     * Send a close frame and close the connection.
     *
     * Later read() calls return null. Calling close() again does nothing.
     */
    public function close(): void
    {
        if (!\is_resource($this->socket)) {
            return;
        }

        try {
            // A close frame without a status code (RFC 6455 section 5.5.1). The
            // daemon may already have closed its side, so a failed write is ignored.
            $this->sendFrame(self::OPCODE_CLOSE, '');
        } catch (\RuntimeException) {
        }
        fclose($this->socket);
    }

    /**
     * Wait for the next data frame.
     *
     * Ping frames are answered with a pong and a close frame closes the stream;
     * neither is returned. Docker sends output as it is written, so a frame is a
     * chunk of output rather than a line or a whole message.
     *
     * @param int  $waitTime      Time to wait in seconds before return false
     * @param int  $waitMicroTime Time to wait in microseconds before return false
     * @param bool $getFrame      Whether to return the frame of websocket or only the data
     *
     * @return false|string|array|null Null once the stream is closed, false if no frame arrived in time, the frame data, or the frame array if $getFrame is set to true
     */
    public function read($waitTime = 0, $waitMicroTime = 200000, $getFrame = false)
    {
        while (true) {
            if (!\is_resource($this->socket) || feof($this->socket)) {
                return null;
            }

            $read = [$this->socket];
            $write = null;
            $expect = null;
            $selected = @stream_select($read, $write, $expect, $waitTime, $waitMicroTime);
            if (false === $selected) {
                throw new \RuntimeException('Cannot wait for the WebSocket stream.');
            }
            if (0 === $selected) {
                return false;
            }

            $frame = $this->readFrame();
            if (null === $frame) {
                // The daemon closed the connection before sending a whole frame.
                fclose($this->socket);

                return null;
            }

            if (self::OPCODE_CLOSE === $frame['opcode']) {
                $this->close();

                return null;
            }
            if (self::OPCODE_PING === $frame['opcode']) {
                $this->sendFrame(self::OPCODE_PONG, $frame['data']);
                continue;
            }
            if (self::OPCODE_PONG === $frame['opcode']) {
                continue;
            }

            return $getFrame ? $frame : $frame['data'];
        }
    }

    /**
     * Read one frame, or return null when the connection ends first.
     */
    private function readFrame(): ?array
    {
        $header = $this->socketRead(2);
        if (2 !== \strlen($header)) {
            return null;
        }
        $firstByte = \ord($header[0]);
        $secondByte = \ord($header[1]);

        $frame = [
            'fin' => ($firstByte & 128) >> 7,
            'rsv1' => ($firstByte & 64) >> 6,
            'rsv2' => ($firstByte & 32) >> 5,
            'rsv3' => ($firstByte & 16) >> 4,
            'opcode' => $firstByte & 15,
            'mask' => ($secondByte & 128) >> 7,
            'len' => $secondByte & 127,
        ];

        if (126 === $frame['len'] || 127 === $frame['len']) {
            $size = 126 === $frame['len'] ? 2 : 8;
            $length = $this->socketRead($size);
            if ($size !== \strlen($length)) {
                return null;
            }
            $frame['len'] = unpack(2 === $size ? 'n' : 'J', $length)[1];
        }

        if (1 === $frame['mask']) {
            $frame['mask_key'] = $this->socketRead(4);
            if (4 !== \strlen($frame['mask_key'])) {
                return null;
            }
        }

        $frame['data'] = $this->socketRead($frame['len']);
        if ($frame['len'] !== \strlen($frame['data'])) {
            return null;
        }

        if (1 === $frame['mask']) {
            $frame['data'] = $this->mask($frame['data'], $frame['mask_key']);
        }

        return $frame;
    }

    /**
     * Send one masked frame, as clients must (RFC 6455 section 5.3).
     */
    private function sendFrame(int $opcode, string $data): void
    {
        $length = \strlen($data);
        // 126 carries a 16-bit length, so anything above 0xFFFF needs the 64-bit form.
        if ($length > 0xFFFF) {
            $header = \chr(0x80 | 127).pack('J', $length);
        } elseif ($length > 125) {
            $header = \chr(0x80 | 126).pack('n', $length);
        } else {
            $header = \chr(0x80 | $length);
        }
        $maskKey = random_bytes(4);

        $this->socketWrite(\chr(0x80 | $opcode).$header.$maskKey.$this->mask($data, $maskKey));
    }

    private function mask(string $data, string $maskKey): string
    {
        if ('' === $data) {
            return '';
        }

        return $data ^ str_repeat($maskKey, intdiv(\strlen($data), 4) + 1);
    }

    /**
     * Read exactly $length bytes, or fewer if the connection ends first.
     */
    private function socketRead(int $length): string
    {
        $read = '';
        // Empty frames carry no payload; fread() rejects a zero length.
        while (\strlen($read) < $length && !feof($this->socket)) {
            $chunk = @fread($this->socket, $length - \strlen($read));
            // A stalled read returns false or '' depending on the PHP version.
            if (stream_get_meta_data($this->socket)['timed_out']) {
                throw new \RuntimeException('Timed out reading a WebSocket frame.');
            }
            if (false === $chunk) {
                throw new \RuntimeException('Cannot read from the WebSocket stream.');
            }
            $read .= $chunk;
        }

        return $read;
    }

    private function socketWrite(string $data): void
    {
        while ('' !== $data) {
            $written = @fwrite($this->socket, $data);
            if (false === $written || 0 === $written) {
                throw new \RuntimeException('Cannot write to the WebSocket stream.');
            }
            $data = (string) substr($data, $written);
        }
    }
}
