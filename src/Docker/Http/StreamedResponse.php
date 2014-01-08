<?php

namespace Docker\Http;

class StreamedResponse extends Response
{
    /**
     * Stream ressource to fetch new data of incoming response
     *
     * @var ressource
     */
    private $stream;

    /**
     * Stream ressource to set
     *
     * @param ressource $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * Fetch rest of incoming response
     *
     * @param callable $callback Callback to call, this
     */
    public function read(callable $callback = null)
    {
        if ($this->stream === null) {
            return parent::read($callback);
        }

        do {
            $sizeLine    = fgets($this->stream);
            $chunkedSize = hexdec(trim($sizeLine));

            if ($chunkedSize <= 0) {
                break;
            }

            $content = fread($this->stream, $chunkedSize);
            $this->addContent($content);

            if ($callback !== null) {
                $callback($content);
            }

            fgets($this->stream);
        } while (!feof($this->stream));

        if ($this->headers->get('Connection') == 'close') {
            fclose($this->stream);
            $this->stream = null;
        }
    }
}