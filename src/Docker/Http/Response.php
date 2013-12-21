<?php

namespace Docker\Http;

/**
 * Docker\Http\Response
 */
class Response
{
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
     * @param callable $callback Callback to call, this
     */
    public function read(callable $callback)
    {
        return $callback($this->getContent());
    }
}