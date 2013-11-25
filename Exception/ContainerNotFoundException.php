<?php

namespace Docker\Exception;

use Docker\Exception;

class ContainerNotFoundException extends Exception
{
    public function __construct($containerId)
    {
        parent::__construct(sprintf('Container not found: "'.$containerId.'"'), 404);
    }
}