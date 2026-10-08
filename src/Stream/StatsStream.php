<?php

declare(strict_types=1);

namespace Docker\Stream;

/**
 * Live container resource usage from the stats endpoint.
 *
 * Callables passed to onFrame() receive each sample as a decoded \stdClass,
 * the same shape containerStats() returns with `stream => false`.
 */
class StatsStream extends CallbackStream
{
    use ReadsJsonDocuments;

    protected function readFrame()
    {
        $jsonFrame = $this->readJsonDocument();
        if (null === $jsonFrame) {
            return null;
        }

        return json_decode($jsonFrame, false, 512, \JSON_THROW_ON_ERROR);
    }
}
