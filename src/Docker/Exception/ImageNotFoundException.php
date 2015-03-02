<?php

namespace Docker\Exception;

use Docker\Exception as BaseException;
use Exception;

/**
 * Docker\Exception\ImageNotFoundException
 */
class ImageNotFoundException extends BaseException
{
    /**
     * @param string         $imageId
     * @param null|Exception $previous
     */
    public function __construct($imageId, Exception $previous = null)
    {
        parent::__construct(sprintf('Image not found: "%s"', $imageId), 404, $previous);
    }
}
