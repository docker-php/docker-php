<?php

namespace Docker\Http;

class Request
{
    private $method;

    private $uri;

    private $protocolVersion = '1.1';

    private $headers = array();

    private $content;

    private $compiler;

    public function __construct($method, $uri, $headers = array())
    {
        $this->method = strtolower($method);
        $this->uri = $uri;
        $this->compiler = new UriTemplate();

        $this->setHeaders($headers);

        # this is to make sure protocol specific headers are set/unset
        $this->setProtocolVersion($this->protocolVersion);
    }

    public function __toString()
    {
        $message  = vsprintf('%s %s HTTP/%s', [
            strtoupper($this->method),
            $this->getRequestUri(),
            $this->protocolVersion
        ])."\r\n";

        if (count($this->headers) > 0) {
            $message .= $this->formatHeaders()."\r\n";
        }

        $message .= "\r\n";

        if (strlen($this->getContent()) > 0) {
            $message .= $this->getContent();
        }

        return $message;
    }

    public function getRequestUri()
    {
        $requestUri = $this->uri;

        if (is_array($requestUri)) {
            $requestUri = $this->compiler->compile($requestUri[0], $requestUri[1]);
        }

        return $requestUri;
    }

    public function setContent($content, $contentType = null)
    {
        $this->content = $content;

        $this->setContentLength(mb_strlen($content));

        if (null !== $contentType) {
            $this->setContentType($contentType);
        }

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;

        if ($protocolVersion === '1.1') {
            $this->setHeader('connection', 'close');
        } else {
            $this->removeHeader('connection');
        }

        return $this;
    }

    public function setContentLength($contentLength)
    {
        $this->setHeader('content-length', (integer) $contentLength);

        return $this;
    }

    public function setContentType($contentType)
    {
        $this->setHeader('content-type', $contentType);

        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->headers[strtolower($name)] = $value;

        return $this;
    }

    public function removeHeader($name)
    {
        $key = strtolower($name);

        if (array_key_exists($key, $this->headers)) {
            unset($this->headers[$key]);
        }

        return $this;
    }

    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    public function hasHeader($name)
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    private function formatHeaders()
    {
        $headers = [];

        foreach ($this->headers as $name => $value) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            $headers[] = sprintf('%s: %s', $name, $value);
        }

        sort($headers);

        return implode("\r\n", $headers);
    }
}