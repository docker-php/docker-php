<?php

namespace Docker\Exception;

use Docker\Exception;
use Exception as BaseException;

/**
 * Docker\Exception\ImageNotFoundException
 */
class ImageNotFoundException extends BaseException
{
    /**
     * @param string         $imageId
     * @param null|BaseException $previous
     */
    public function __construct($imageId, BaseException $previous = null)
    {
        parent::__construct(sprintf('Image not found: "'.$imageId.'"'), 404, $previous);
    }
}