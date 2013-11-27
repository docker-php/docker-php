<?php

namespace Docker\Exception;

use Docker\Exception;

use Exception as BaseException;

class PortNotFoundException extends BaseException
{
    public function __construct($port, $protocol)
    {
        parent::__construct(sprintf('Port not found: %s/%s', $port, $protocol));
    }
}