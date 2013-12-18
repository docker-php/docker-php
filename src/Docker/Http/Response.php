<?php

namespace Docker\Http;

class Response
{
    private $protocolVersion;

    private $statusCode;

    private $statusText;

    public $headers;

    private $content;

    public function __construct()
    {
        $this->headers = new HeaderBag();
    }

    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    public function json($assoc = false, $depth = 512, $options = 0)
    {
        return json_decode($this->getContent(), $assoc, $depth, $options);
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = (integer) $statusCode;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusText($statusText)
    {
        $this->statusText = $statusText;

        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getContentLength()
    {
        if (array_key_exists('content-length', $this->headers)) {
            return (integer) $this->headers->get('content-length');
        }

        return 0;
    }
}