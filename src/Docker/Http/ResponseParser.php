<?php

namespace Docker\Http;

use RuntimeException;

class ResponseParser
{
    public function parse($raw)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $raw);
        rewind($stream);

        return $this->parseStream($stream);
    }

    public function parseStream($stream)
    {
        $fetchHeaders = true;

        $response = new Response();
        $content = '';

        while (!feof($stream)) {
            $line = fgets($stream);

            if (null === $response->getStatusCode()) {
                if (!preg_match(',HTTP/(1\.(?:1|0)) (\d{3}) (.+),', trim($line), $matches)) {
                    throw new RuntimeException(sprintf('Could not find a status code in "%s"', $line));
                }

                $response->setProtocolVersion($matches[1]);
                $response->setStatusCode($matches[2]);
                $response->setStatusText($matches[3]);

                continue;
            }

            if (strlen(trim($line)) === 0) {
                $fetchHeaders = false;

                continue;
            }

            if ($fetchHeaders) {
                $header = explode(':', trim($line));

                if (count($header) < 2) {
                    throw new RuntimeException(sprintf('Malformed header "%s"', $line));
                }

                $response->setHeader($header[0], trim(implode(':', array_slice($header, 1))));

                continue;
            }

            $content .= $line;

            if ($response->getContentLength() === mb_strlen($content)) {
                break;
            }
        }

        $response->setContent($content);

        return $response;
    }
}