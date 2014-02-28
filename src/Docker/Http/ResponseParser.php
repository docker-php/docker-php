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
        $content  = "";
        $parser   = new MessageParser();
        $gotStatus = false;

        //First create response with only headers
        while (($line = fgets($stream)) !== false) {
            $gotStatus = $gotStatus || (strpos($line, 'HTTP') !== false);
            if ($gotStatus) {
                $content .= $line;

                if (rtrim($line) === '') {
                    break;
                }
            }
        }

        $metadata = stream_get_meta_data($stream);

        if ($metadata['timed_out']) {
            throw new TimeoutException();
        }

        $infos = $parser->parseResponse($content);

        if (isset($infos['headers']['Transfer-Encoding']) && $infos['headers']['Transfer-Encoding'] == 'chunked') {
            $response = new StreamedResponse();
        } else {
            $response = new Response();
        }

        $response->setSocket($stream);
        $response->setStatusCode($infos['code']);
        $response->setStatusText($infos['reason_phrase']);
        $response->setProtocolVersion($infos['version']);
        $response->headers->replace($infos['headers']);

        if ($response instanceof StreamedResponse) {
            return $response;
        }

        //Get all remaining content
        $content  = stream_get_contents($stream);
        $response->setContent($content);

        return $response;
    }
}