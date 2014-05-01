<?php
/**
 * Created by PhpStorm.
 * User: brouznouf
 * Date: 01/05/2014
 * Time: 21:03
 */

namespace Docker\Http\Stream;

use GuzzleHttp\Stream\Stream;

class ChunkedStream extends Stream implements StreamCallbackInterface
{
    /**
     * Read a stream by block
     *
     * @param callable $callback
     *
     * @return sring
     */
    public function readWithCallback(callable $callback)
    {
        while (!$this->eof()) {
            $callback($this->readPart(), null);
        }
    }

    protected function readPart()
    {
        $tmpSize = "";

        while(($read = (string)$this->read(1)) != "\n") {
            $tmpSize .= $read;
        }

        $size = hexdec(trim($tmpSize));

        if ($size > 0) {
            $part = $this->read($size);
        } else {
            $part = "";
        }

        while($read = $this->read(1) != "\n");

        return $part;
    }
} 