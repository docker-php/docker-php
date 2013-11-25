<?php

namespace Docker\Exception;

use Docker\Exception;

class PortNotFoundException extends Exception
{
    public function __construct($port, $protocol)
    {
        parent::__construct(sprintf('Port not found: %s/%s', $port, $protocol));
    }
}