<?php

namespace Docker\Http;

use Guzzle\Parser\UriTemplate\UriTemplate;

class Request
{
    private $method;

    private $uri;

    private $protocolVersion = '1.1';

    public $headers;

    private $content;

    private $expander;

    public function __construct($method, $uri, $headers = array(), $expander = null)
    {
        $this->method = strtolower($method);
        $this->uri = $uri;
        $this->expander = $expander ?: new UriTemplate();
        $this->headers = new HeaderBag($headers);

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
            $message .= $this->headers->format()."\r\n";
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
            $requestUri = $this->expander->expand($requestUri[0], $requestUri[1]);
        }

        return $requestUri;
    }

    public function setContent($content, $contentType = null)
    {
        $this->content = $content;

        $this->headers->set('content-length', mb_strlen($content));

        if (null !== $contentType) {
            $this->headers->set('content-type', $contentType);
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
            $this->headers->set('connection', 'close');
        } else {
            $this->headers->remove('connection');
        }

        return $this;
    }
}