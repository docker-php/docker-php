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

        if (strlen($header) < 8) {
            return array(null, null);
        }

        $decoded = unpack('C1stream_type/C3/N1size', $header);

        return array($this->read($decoded['size']), $decoded['stream_type']);
    }
}
