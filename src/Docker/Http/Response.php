<?php

namespace Docker\Http;

use Docker\Http\Exception\TimeoutException;
/**
 * Docker\Http\Response
 */
class Response
{
    /**
     * Stream ressource to fetch new data of incoming response
     *
     * @var ressource
     */
    protected $stream;

    /**
     * Is the stream blocked
     *
     * @var ressource
     */
    protected $blocking = true;

    /**
     * @var string
     */
    private $protocolVersion;

    /**
     * @var integer
     */
    private $statusCode;

    /**
     * @var string
     */
    private $statusText;

    /**
     * @var Docker\Http\HeaderBag
     */
    public $headers;

    /**
     * @var string
     */
    private $content;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->headers = new HeaderBag();
    }

    /**
     * @param string $protocolVersion
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * @param boolean $assoc
     * @param integer $depth
     * @param integer $options
     *
     * @return array|stdClass
     */
    public function json($assoc = false, $depth = 512, $options = 0)
    {
        return json_decode($this->getContent(), $assoc, $depth, $options);
    }

    /**
     * @param integer $statusCode
     *
     * @return Docker\Http\Response
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = (integer) $statusCode;

        return $this;
    }

    /**
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $statusText
     *
     * @return Docker\Http\Response
     */
    public function setStatusText($statusText)
    {
        $this->statusText = $statusText;

        return $this;
    }

    /**
     * Append new content
     *
     * @param string $content New content to append to actual content
     *
     * @return Docker\Http\Response
     */
    public function addContent($content)
    {
        $this->content .= $content;

        return $this;
    }

    /**
     * @param string
     *
     * @return Docker\Http\Response
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Fetch rest of incoming response
     *
     * @param callable $callback Callback to call
     *
     * Callback must be of the following format :
     * funtion ($output) {
     *
     * }
     */
    public function read(callable $callback = null)
    {
        if (!empty($this->content) && $callback !== null) {
            $callback($this->getContent());
        }

        if ($this->stream === null) {
            return;
        }

        do {
            $content = $this->readLine($this->stream);

            if (false === $content) {
                break;
            }

            $this->addContent($content);

            if ($callback !== null) {
                $callback($content);
            }
        } while (!feof($this->stream));

        $metadata = stream_get_meta_data($this->stream);

        if ($this->headers->get('Connection') == 'close') {
            fclose($this->stream);
            $this->stream = null;
        }

        if ($metadata['timed_out']) {
            throw new TimeoutException();
        }
    }

    /**
     * Fetch response with docker protocol to handle terminal like response
     *
     * @param callable $callback Callback to call
     */
    public function readAttach(callable $callback = null)
    {
        $this->read(function ($payload) use($callback) {
            while (!empty($payload)) {
                $header  = substr($payload, 0, 8);
                $decoded = unpack('C1stream_type/C3/N1size', $header);
                $content = substr($payload, 8, $decoded['size']);
                $payload = substr($payload, 8 + $decoded['size']);

                $callback($decoded['stream_type'], $content);
            }
        });
    }

    /**
     * Stream resource to set
     *
     * @param resource $stream
     * @param boolean  $blocking
     */
    public function setStream($stream, $blocking = true)
    {
        $this->stream = $stream;
        $this->blocking = $blocking;
    }

    /**
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Read a line from a stream
     *
     * @param resource $stream
     *
     * @return string|false Return content or false if no content left
     */
    protected function readLine($stream)
    {
        $content = fgets($stream);

        return $content;
    }
}