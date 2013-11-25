<?php

namespace Docker\Exception;

use Docker\Exception;

class UnexpectedStatusCodeException extends Exception
{
    public function __construct($statusCode)
    {
        parent::__construct(null, $statusCode);
    }
}