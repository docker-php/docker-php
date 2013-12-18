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

        $response = $this->parser->parseStream($socket);

        fclose($socket);

        return $response;
    }

    /**
     * @param string $uri
     * 
     * @return Docker\Http\Response
     */
    public function get($uri)
    {
        return new Request('GET', $uri, $this->getDefaultHeaders());
    }

    /**
     * @param string $uri
     * 
     * @return Docker\Http\Response
     */
    public function post($uri)
    {
        return new Request('POST', $uri, $this->getDefaultHeaders());
    }

    /**
     * @param string $uri
     * 
     * @return Docker\Http\Response
     */
    public function delete($uri)
    {
        return new Request('DELETE', $uri, $this->getDefaultHeaders());
    }
}