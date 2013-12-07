<?php

namespace Docker\Exception;

use Docker\Exception;

use Exception as BaseException;

/**
 * Docker\Exception\ContainerNotFoundException
 */
class ContainerNotFoundException extends BaseException
{
    /**
     * @param string         $containerId
     * @param Exception|null $previous
     */
    public function __construct($containerId, BaseException $previous = null)
    {
        parent::__construct(sprintf('Container not found: "'.$containerId.'"'), 404, $previous);
    }
}