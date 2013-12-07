<?php

namespace Docker\Exception;

use Docker\Exception;

use Exception as BaseException;

/**
 * Docker\Exception\UnexpectedStatusCodeException
 */
class UnexpectedStatusCodeException extends BaseException
{
    /**
     * @param integer $statusCode
     */
    public function __construct($statusCode)
    {
        parent::__construct(null, (integer) $statusCode);
    }
}