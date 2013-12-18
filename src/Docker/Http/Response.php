<?php

namespace Docker\Http;

class Response
{
    private $protocolVersion;

    private $statusCode;

    private $statusText;

    private $headers = array();

    private $content;

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

    public function setHeader($name, $value)
    {
        $this->headers[strtolower($name)] = $value;

        return $this;
    }

    public function getHeader($name)
    {
        $key = strtolower($name);

        if (array_key_exists($key, $this->headers)) {
            return $this->headers[$key];
        }

        return null;
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
            return (integer) $this->headers['content-length'];
        }

        return 0;
    }
}