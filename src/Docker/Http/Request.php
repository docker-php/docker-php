<?php

namespace Docker\Http;

use Guzzle\Parser\UriTemplate\UriTemplate;

/**
 * Docker\Http\Request
 */
class Request
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var string|array
     */
    private $uri;

    /**
     * @var string
     */
    private $protocolVersion = '1.1';

    /**
     * @var Docker\Http\HeaderBag
     */
    public $headers;

    /**
     * @var string
     */
    private $content;

    /**
     * @var Guzzle\Parser\UriTemplate\UriTemplate
     */
    private $expander;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @param string                                $method
     * @param string|array                          $uri
     * @param array                                 $headers
     * @param Guzzle\Parser\UriTemplate\UriTemplate $expander
     */
    public function __construct($method, $uri, $headers = array(), $expander = null)
    {
        $this->method = strtolower($method);
        $this->uri = $uri;
        $this->expander = $expander ?: new UriTemplate();
        $this->headers = new HeaderBag($headers);

        # this is to make sure protocol specific headers are set/unset
        $this->setProtocolVersion($this->protocolVersion);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $message = $this->getHeadersAsString();
        $content = $this->getContent();

        if (is_resource($this->getContent())) {
            //Send as a chunked message
            $content = stream_get_contents($this->getContent());
            $message .= dechex(mb_strlen($content))."\n";
            $message .= $content;
        } elseif (strlen($this->getContent()) > 0) {
            $message .= $this->getContent();
        }

        return $message;
    }

    /**
     * @param integer $timeout
     *
     * @return Docker\Http\Request
     */
    public function setTimeout($timeout = null)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        $requestUri = $this->uri;

        if (is_array($requestUri)) {
            $requestUri = $this->expander->expand($requestUri[0], $requestUri[1]);
        }

        return $requestUri;
    }

    /**
     * Set the content of a request, if a stream is passed it will simply copy stream
     *
     * @param string|resource $content Content to send
     * @param string          $contentType
     *
     * @return Docker\Http\Request
     */
    public function setContent($content, $contentType = null)
    {
        $this->content = $content;

        if (is_string($content)) {
            $this->headers->set('Content-Length', mb_strlen($content));
        }

        if (null !== $contentType) {
            $this->headers->set('Content-Type', $contentType);
        }

        if (is_resource($content)) {
            $this->headers->set('Transfer-Encoding', 'chunked');
        }

        return $this;
    }

    /**
     * Get content of request
     *
     * @return string|resource
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $protocolVersion
     *
     * @return Docker\Http\Request
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;

        if ($protocolVersion === '1.1') {
            $this->headers->set('Connection', 'close');
        } else {
            $this->headers->remove('Connection');
        }

        return $this;
    }

    /**
     * Get http headers as a string
     *
     * @return string
     */
    public function getHeadersAsString()
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

        return $message;
    }
}