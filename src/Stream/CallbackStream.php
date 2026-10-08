<?php

declare(strict_types=1);

namespace Docker\Stream;

use Psr\Http\Message\StreamInterface;

abstract class CallbackStream
{
    protected $stream;

    private $onNewFrameCallables = [];

    private bool $stopped = false;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Called when there is a new frame from the stream.
     */
    public function onFrame(callable $onNewFrame): void
    {
        $this->onNewFrameCallables[] = $onNewFrame;
    }

    /**
     * Read a frame in the stream.
     */
    abstract protected function readFrame();

    /**
     * Wait for stream to finish and call callables if defined.
     */
    public function wait(): void
    {
        while (!$this->stopped && !$this->stream->eof()) {
            $frame = $this->readFrame();

            if (null !== $frame) {
                if (!\is_array($frame)) {
                    $frame = [$frame];
                }

                foreach ($this->onNewFrameCallables as $newFrameCallable) {
                    \call_user_func_array($newFrameCallable, $frame);
                }
            }
        }
    }

    /**
     * Stop reading and close the response body.
     *
     * Called from a frame callback, wait() returns once the remaining callbacks
     * for that frame have run. Closing a build or pull connection can cancel
     * the daemon operation.
     */
    public function stop(): void
    {
        if ($this->stopped) {
            return;
        }

        $this->stopped = true;
        $this->stream->close();
    }

    public function closeAndRead(): void
    {
        $this->stream->close();
        $this->wait();
    }
}
