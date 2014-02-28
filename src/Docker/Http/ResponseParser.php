<?php

namespace Docker\Http;

use Docker\Http\Exception\TimeoutException;
use Docker\Http\Exception\ParseErrorException;
use Docker\Http\Request;

use Guzzle\Parser\Message\MessageParser;

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
    public function parse($stream, $blocking = true)
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
            throw new TimeoutException('Timed out while reading socket');
        }

        $infos = $parser->parseResponse($content);

        if (isset($infos['headers']['Transfer-Encoding']) && $infos['headers']['Transfer-Encoding'] == 'chunked') {
            $response = new ChunkedResponse();
        } else {
            $response = new Response();
        }

        $response->setStream($stream, $blocking);
        $response->setStatusCode($infos['code']);
        $response->setStatusText($infos['reason_phrase']);
        $response->setProtocolVersion($infos['version']);
        $response->headers->replace($infos['headers']);

        if (!$blocking) {
            return $response;
        }

        //Get all remaining content
        $response->read();

        return $response;
    }
}