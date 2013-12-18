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
     * @param string $content
     * @param string $contentType
     * 
     * @return Docker\Http\Request
     */
    public function setContent($content, $contentType = null)
    {
        $this->content = $content;

        $this->headers->set('content-length', mb_strlen($content));

        if (null !== $contentType) {
            $this->headers->set('content-type', $contentType);
        }

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
     * @param string $protocolVersion
     * 
     * @return Docker\Http\Request
     */
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