<?php

namespace Docker\Http;

use Docker\Http\Exception\TimeoutException;

class AttachResponse extends Response
{
    /**
     * {@inheritDoc}
     */
    protected function readLine($stream)
    {
        $header  = fread($stream, 8);

        if (strlen($header) < 8) {
            return false;
        }

        $decoded = unpack('C1stream_type/C3/N1size', $header);

        return array(fread($stream, $decoded['size']), $decoded['stream_type']);
    }
}