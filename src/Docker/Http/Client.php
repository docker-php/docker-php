<?php

namespace Docker\Http;

use Docker\Http\Exception as HttpException;

/**
 * Docker\Http\Client
 */
class Client
{
    const CHUNK_SIZE = 8192;

    /**
     * @var resource
     */
    private $socket;

    /**
     * @var Docker\Http\ResponseParser
     */
    private $parser;

    /**
     * @var string
     */
    private $userAgent = 'Docker-PHP';

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @param string $spec
     * @param integer $timeout
     */
    public function __construct($spec)
    {
        $this->spec = $spec;
        $this->parser = new ResponseParser();
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
     * @return array
     */
    public function getDefaultHeaders()
    {
        return [
            'Host' => $this->spec,
            'User-Agent' => $this->userAgent,
        ];
    }

    /**
     * @param Docker\Http\Request $request
     * @param boolean             $blocking Do we have to wait for content (always blocking for headers) default is true
     *
     * @return Docker\Http\Response
     */
    public function send(Request $request, $blocking = true)
    {
        $socket = stream_socket_client($this->spec);
        fwrite($socket, $request->getHeadersAsString());
        $content = $request->getContent();

        if (is_resource($content)) {
            while (false !== ($read = fread($content, self::CHUNK_SIZE))) {
                $frame = dechex(mb_strlen($read))."\r\n".$read."\r\n";
                fwrite($socket, $frame);

                if (empty($read)) {
                    break;
                }
            }

            fclose($content);
            fwrite($socket, "0\r\n\r\n");
        } else {
            fwrite($socket, $content);
        }

        $timeout = $request->getTimeout() ?: $this->timeout;

        if (null !== $timeout) {
            stream_set_timeout($socket, $request->getTimeout() ?: $this->timeout);
        }

        try {
            $response = $this->parser->parse($socket, $blocking);
        } catch (HttpException $e) {
            $e->setRequest($request);

            throw $e;
        }

        return $response;
    }

    /**
     * @param string $uri
     *
     * @return Docker\Http\Request
     */
    public function get($uri, $headers = array())
    {
        $headers = array_replace($this->getDefaultHeaders(), $headers);

        return new Request('GET', $uri, $headers);
    }

    /**
     * @param string $uri
     *
     * @return Docker\Http\Request
     */
    public function post($uri, $headers = array())
    {
        $headers = array_replace($this->getDefaultHeaders(), $headers);

        return new Request('POST', $uri, $headers);
    }

    /**
     * @param string $uri
     *
     * @return Docker\Http\Request
     */
    public function delete($uri, $headers = array())
    {
        $headers = array_replace($this->getDefaultHeaders(), $headers);

        return new Request('DELETE', $uri, $headers);
    }
}