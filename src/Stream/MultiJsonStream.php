<?php

namespace Docker\Stream;

/**
 * Represent a stream that decode a stream with multiple json in it
 *
 * Callable passed to this stream will take a stdClass object as first argument (decoded from the json)
 */
class MultiJsonStream extends CallbackStream
{
    /**
     * {@inheritdoc}
     */
    protected function readFrame()
    {
        $jsonFrameEnd = false;
        $lastJsonChar = '';
        $inquote      = false;
        $jsonFrame    = "";
        $level        = 0;

        while (!$jsonFrameEnd && !$this->stream->eof()) {
            $jsonChar   = $this->stream->read(1);

            if ((boolean)($jsonChar == '"' && $lastJsonChar != '\\')) {
                $inquote = !$inquote;
            }

            if (!$inquote && in_array($jsonChar, [" ", "\r", "\n", "\t"])) {
                continue;
            }

            if (!$inquote && in_array($jsonChar, ['{', '['])) {
                $level++;
            }

            if (!$inquote && in_array($jsonChar, ['}', ']'])) {
                $level--;

                if ($level == 0) {
                    $jsonFrameEnd = true;
                    $jsonFrame .= $jsonChar;

                    continue;
                }
            }

            $jsonFrame .= $jsonChar;
        }

        // Invalid last json, or timeout, or connection close before receiving
        if (!$jsonFrameEnd) {
            return null;
        }

        return json_decode($jsonFrame);
    }
}
