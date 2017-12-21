<?php

declare(strict_types=1);

namespace Docker\Stream;

/**
 * Represent a stream when building a dockerfile.
 *
 * Callable(s) passed to this stream will take a BuildInfo object as the first argument
 */
class BuildStream extends MultiJsonStream
{
    /**
     * [@inheritdoc}.
     */
    protected function getDecodeClass()
    {
        return 'BuildInfo';
    }
}
