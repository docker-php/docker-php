<?php

namespace Docker\Exception;

use Docker\Exception;

use Exception as BaseException;

class UnexpectedStatusCodeException extends BaseException
{
    public function __construct($statusCode)
    {
        parent::__construct(null, $statusCode);
    }
}