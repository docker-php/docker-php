<?php

namespace Docker\Exception;

use Docker\Exception;

use Exception as BaseException;

class ContainerNotFoundException extends BaseException
{
    public function __construct($containerId, BaseException $previous = null)
    {
        parent::__construct(sprintf('Container not found: "'.$containerId.'"'), 404, $previous);
    }
}