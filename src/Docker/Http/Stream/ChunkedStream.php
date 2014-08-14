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
     * @return string
     */
    public function readWithCallback(callable $callback)
    {
        while (!$this->eof()) {
            $content = $this->readPart();

            if ($content == null) {
                break;
            }

            $callback($content, null);
        }
    }


    /**
     * Cast stream to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getContents();
    }


    /**
     * Gets the string contents of the stream
     *
     * @param integer $maxlength  Returns contents truncated to supplied length (-1 for max)
     *
     * @return string
     */
    public function getContents($maxlength = -1)
    {
        $contents = '';

        while (!$this->eof()) {
            $part = $this->readPart();

            if ($part == null) {
                break;
            }

            $contents .= $part;

            if ($maxlength > -1 && strlen($contents) > $maxlength) {
                return substr($contents, 0, $maxlength);
            }
        }

        return $contents;
    }


    /**
     * Read a chunk from the stream
     *
     * @return string
     */
    protected function readPart()
    {
        $tmpSize = "";

        while(($read = (string)$this->read(1)) != "\n") {
            $tmpSize .= $read;

            if ($this->eof()) {
                return null;
            }
        }

        $size = hexdec(trim($tmpSize));

        if ($size > 0) {
            $part = $this->read($size);
        } else {
            $part = "";
        }

        while($this->read(1) != "\n") {
            if ($this->eof()) {
                return null;
            }
        }

        return $part;
    }
}
