<?php

namespace Docker\Http;

use Docker\Http\Exception\TimeoutException;

class ChunkedResponse extends Response
{
    /**
     * {@inheritDoc}
     */
    protected function readLine($stream)
    {
        $sizeLine    = fgets($stream);
        $chunkedSize = hexdec(trim($sizeLine));

        if ($chunkedSize <= 0) {
            return false;
        }

        $content = fread($stream, $chunkedSize);

        fgets($stream);

        return $content;
    }
}