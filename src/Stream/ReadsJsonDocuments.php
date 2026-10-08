<?php

declare(strict_types=1);

namespace Docker\Stream;

/**
 * Split a body containing consecutive JSON documents into single documents.
 */
trait ReadsJsonDocuments
{
    /**
     * Read the next complete JSON document from $this->stream.
     *
     * Returns null when the body ends before a document is complete.
     */
    protected function readJsonDocument(): ?string
    {
        $jsonFrameEnd = false;
        $lastJsonChar = '';
        $inquote = false;
        $jsonFrame = '';
        $level = 0;

        while (!$jsonFrameEnd && !$this->stream->eof()) {
            $jsonChar = $this->stream->read(1);

            if ('"' === $jsonChar && '\\' !== $lastJsonChar) {
                $inquote = !$inquote;
            }

            // We ignore white space when it is not part of a quoted string.
            if (!$inquote && \in_array($jsonChar, [' ', "\r", "\n", "\t"], true)) {
                continue;
            }

            if (!$inquote && \in_array($jsonChar, ['{', '['], true)) {
                ++$level;
            }

            if (!$inquote && \in_array($jsonChar, ['}', ']'], true)) {
                --$level;

                if (0 === $level) {
                    $jsonFrameEnd = true;
                    $jsonFrame .= $jsonChar;
                    $lastJsonChar = '';
                    continue;
                }
            }

            $jsonFrame .= $jsonChar;
            $lastJsonChar = $jsonChar;
        }

        // Invalid last json, or timeout, or connection close before receiving
        if (!$jsonFrameEnd) {
            return null;
        }

        return $jsonFrame;
    }
}
