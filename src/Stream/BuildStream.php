<?php

namespace Docker\Stream;

/**
 * Represent a stream when building a dockerfile
 *
 * Callable(s) passed to this stream will take a BuildFrame object as the first argument
 */
class BuildStream extends MultiJsonStream
{
    /**
     * {@inheritdoc}
     *
     * @return BuildFrame|null
     */
    protected function readFrame()
    {
        $frame = parent::readFrame();

        if ($frame === null) {
            return null;
        }

        $buildFrame = new BuildFrame();

        if (isset($frame->stream)) {
            $buildFrame->setStream($frame->stream);
        }

        return $buildFrame;
    }
}
