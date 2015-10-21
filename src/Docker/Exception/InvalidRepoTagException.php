<?php

namespace Docker\Exception;

use Docker\Exception as BaseException;

/**
 * Docker\Exception\PortNotFoundException
 */
class InvalidRepoTagException extends BaseException
{
    /**
     * @param string  $repoTag
     */
    public function __construct($repoTag)
    {
        parent::__construct(sprintf('Invalid repoTag: %s', $repoTag));
    }
}
