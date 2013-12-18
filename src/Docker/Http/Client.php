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
     * @param string $spec
     */
    public function __construct($spec)
    {
        $this->spec = $spec;
        $this->socket = stream_socket_client($spec);
        $this->parser = new ResponseParser();
    }

    /**
     * @param Docker\Http\Request $request
     * 
     * @return Docker\Http\Response
     */
    public function send(Request $request)
    {
        fwrite($this->socket, (string) $request);

        return $this->parser->parseStream($this->socket);
    }

    /**
     * @param string $uri
     * 
     * @return Docker\Http\Response
     */
    public function get($uri)
    {
        return new Request('GET', $uri);
    }

    /**
     * @param string $uri
     * 
     * @return Docker\Http\Response
     */
    public function post($uri)
    {
        return new Request('POST', $uri);
    }

    /**
     * @param string $uri
     * 
     * @return Docker\Http\Response
     */
    public function delete($uri)
    {
        return new Request('DELETE', $uri);
    }
}