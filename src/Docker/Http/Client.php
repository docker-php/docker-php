<?php

namespace Docker\Http;

/**
 * Docker\Http\Client
 */
class Client
{
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
     * @param string $spec
     */
    public function __construct($spec)
    {
        $this->spec = $spec;
        $this->parser = new ResponseParser();
    }

    /**
     * @return array
     */
    public function getDefaultHeaders()
    {
        return [
            'host' => $this->spec,
            'user-agent' => $this->userAgent,
        ];
    }

    /**
     * @param Docker\Http\Request $request
     * 
     * @return Docker\Http\Response
     */
    public function send(Request $request)
    {
        $socket = stream_socket_client($this->spec);

        fwrite($socket, (string) $request);

        $response = $this->parser->parse($socket);

        fclose($socket);

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

        return new Request('POST', $uri, $header);
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