<?php

namespace Docker\Http;

class StreamedResponse extends Response
{
    /**
     * Fetch rest of incoming response
     *
     * @param callable $callback Callback to call, this
     */
    public function read(callable $callback = null)
    {
        if ($this->getSocket() === null) {
            return parent::read($callback);
        }

        do {
            $sizeLine    = fgets($this->getSocket());
            $chunkedSize = hexdec(trim($sizeLine));

            if ($chunkedSize <= 0) {
                break;
            }

            $content = fread($this->getSocket(), $chunkedSize);
            $this->addContent($content);

            if ($callback !== null) {
                $callback($content);
            }

            fgets($this->getSocket());
        } while (!feof($this->getSocket()));

        if ($this->headers->get('Connection') == 'close') {
            $this->close();
        }
    }
}