<?php

declare(strict_types=1);

namespace Docker\Stream;

/**
 * Represent a stream when pushing an image to a repository (with the push api endpoint of image).
 *
 * Callable(s) passed to this stream will take a PushImageInfo object as the first argument
 */
class PushStream extends MultiJsonStream
{
    /**
     * [@inheritdoc}.
     */
    protected function getDecodeClass()
    {
        return 'PushImageInfo';
    }
}
