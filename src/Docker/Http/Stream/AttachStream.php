<?php

namespace Docker\Http\Stream;

use GuzzleHttp\Stream\Stream;

class AttachStream extends Stream implements StreamCallbackInterface
{
    /**
     * Read a stream by block
     *
     * @param callable $callback
     *
     * @return string
     */
    public function readWithCallback(callable $callback)
    {
        while (!$this->eof()) {
            list($content, $type) = $this->readPart();

            if ($content !== null) {
                $callback($content, $type);
            }
        }
    }

    protected function readPart()
    {
        $header = $this->read(8);

        while (strlen($header) < 8) {
            if ($this->eof()) {
                return array(null, null);
            }

            $header .= $this->read(8 - strlen($header));
        }

        $decoded = unpack('C1stream_type/C3/N1size', $header);
        $read    = "";

        do {
            $read .= $this->read($decoded['size'] - strlen($read));
        } while(strlen($read) != $decoded['size']);

        return array($read, $decoded['stream_type']);
    }
}
