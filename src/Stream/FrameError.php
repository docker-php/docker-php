<?php

declare(strict_types=1);

namespace Docker\Stream;

/**
 * Read the error from a build, pull or push progress frame.
 *
 * Docker reports these errors inside an HTTP 200 stream. The generated models
 * differ between API versions: API 1.52 removed the deprecated `error` field,
 * and push frames only have `errorDetail` from API 1.48.
 */
final class FrameError
{
    /**
     * Docker's error message in the frame, or null when the frame has none.
     */
    public static function message(object $frame): ?string
    {
        if (method_exists($frame, 'getErrorDetail')) {
            $message = $frame->getErrorDetail()?->getMessage();
            if (\is_string($message) && '' !== $message) {
                return $message;
            }
        }
        if (method_exists($frame, 'getError')) {
            $message = $frame->getError();
            if (\is_string($message) && '' !== $message) {
                return $message;
            }
        }

        return null;
    }
}
