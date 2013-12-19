<?php

namespace Docker\Http;

use Docker\Http\Exception\TimeoutException;
use Docker\Http\Exception\ParseErrorException;
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
        $message = stream_get_contents($stream);
        $metadata = stream_get_meta_data($stream);

        if ($metadata['timed_out']) {
            throw new TimeoutException();
        }

        $parser = new MessageParser();
        $infos = $parser->parseResponse($message);

        if (false === $infos) {
            throw new ParseErrorException($message);
        }

        $response = new Response();
        $response->setStatusCode($infos['code']);
        $response->setStatusText($infos['reason_phrase']);
        $response->setProtocolVersion($infos['version']);
        $response->headers->replace($infos['headers']);
        $response->setContent($infos['body']);

        return $response;
    }
}