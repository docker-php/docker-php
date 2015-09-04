<?php

namespace Docker\Exception;

use Docker\Exception as BaseException;
use Exception;

/**
 * Docker\Exception\ContainerNotFoundException
 */
class ContainerNotFoundException extends BaseException
{
    /**
     * @param string         $containerId
     * @param null|Exception $previous
     */
    public function __construct($containerId, Exception $previous = null)
    {
        parent::__construct(sprintf('Container not found: "%s"', $containerId), 404, $previous);
    }
}
