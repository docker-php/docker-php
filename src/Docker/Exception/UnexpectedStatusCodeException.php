<?php

namespace Docker\Exception;

use Docker\Exception as BaseException;
use GuzzleHttp\Message\Response;

/**
 * Docker\Exception\UnexpectedStatusCodeException
 */
class UnexpectedStatusCodeException extends BaseException
{
    /**
     * @param integer $statusCode
     * @param string  $message
     */
    public function __construct($statusCode, $message = null)
    {
        $message = $message ?: 'Status Code: '.$statusCode;
        parent::__construct($message, (integer) $statusCode);
    }

    /**
     * @param Response $response
     *
     * @return UnexpectedStatusCodeException
     */
    public static function fromResponse(Response $response)
    {
        return new self($response->getStatusCode(), trim((string) $response->getBody()));
    }
}
