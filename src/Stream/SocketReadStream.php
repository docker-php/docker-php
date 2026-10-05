<?php

declare(strict_types=1);

namespace Docker\Stream;

use Http\Message\Decorator\StreamDecorator;
use Psr\Http\Message\StreamInterface;

/**
 * Drain PHP's socket read buffer before requesting more bytes from the peer.
 */
final class SocketReadStream implements StreamInterface
{
    use StreamDecorator;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public function read(int $length): string
    {
        $buffered = $this->stream->getMetadata('unread_bytes');
        if (\is_int($buffered) && $buffered > 0 && $length > 0) {
            $length = min($length, $buffered);
        }

        return $this->stream->read($length);
    }
}
