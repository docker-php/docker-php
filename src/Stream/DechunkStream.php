<?php

declare(strict_types=1);

namespace Docker\Stream;

/**
 * Return decoded data as it arrives instead of filling the requested read size.
 */
class DechunkStream extends \Http\Message\Encoding\DechunkStream
{
    public function read(int $length): string
    {
        if ($length < 0) {
            throw new \InvalidArgumentException('Read length must not be negative.');
        }
        if (0 === $length) {
            return '';
        }

        while ('' === $this->buffer && !$this->stream->eof()) {
            $this->fill();
        }

        $output = substr($this->buffer, 0, $length);
        $this->buffer = substr($this->buffer, \strlen($output));

        return $output;
    }
}
