<?php

namespace Docker\Http;

use Guzzle\Parser\Message\MessageParser;
use RuntimeException;

/**
 * Docker\Http\ResponseParser
 */
class ResponseParser
{
    /**
     * @param resource
     * 
     * @return Docker\Http\Response
     */
    public function parse($stream)
    {
        $parser = new MessageParser();
        $infos = $parser->parseResponse(stream_get_contents($stream));

        $response = new Response();
        $response->setStatusCode($infos['code']);
        $response->setStatusText($infos['reason_phrase']);
        $response->setProtocolVersion($infos['version']);
        $response->headers->replace($infos['headers']);
        $response->setContent($infos['body']);

        return $response;
    }
}