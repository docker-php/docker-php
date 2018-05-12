<?php

declare(strict_types=1);

namespace Docker\Stream;

use Amp\ByteStream\InputStream;
use Amp\CancellationTokenSource;
use Amp\Promise;
use function Amp\call;

class ArtaxCallbackStream
{
    private $stream;
    private $onNewFrameCallables = [];
    private $chunkTransformer;
    private $cancellationTokenSource;

    public function __construct(
        InputStream $stream,
        CancellationTokenSource $cancellationTokenSource,
        ?callable $chunkTransformer
    ) {
        $this->stream = $stream;
        $this->cancellationTokenSource = $cancellationTokenSource;
        $this->chunkTransformer = $chunkTransformer;
    }

    /**
     * Called when there is a new frame from the stream.
     *
     * @param callable $onNewFrame
     */
    public function onFrame(callable $onNewFrame): void
    {
        $this->onNewFrameCallables[] = $onNewFrame;
    }

    /**
     * Consume stream chunks.
     *
     * @return Promise
     */
    public function listen(): Promise
    {
        return call(function () {
            while (null !== ($chunk = yield $this->stream->read())) {
                foreach ($this->onNewFrameCallables as $newFrameCallable) {
                    $newFrameCallable($this->transformChunk($chunk));
                }
            }
        });
    }

    /**
     * Stop consuming stream chunks.
     */
    public function cancel(): void
    {
        $this->cancellationTokenSource->cancel();
    }

    /**
     * Transform stream chunks if required.
     *
     * @param string $chunk
     *
     * @return mixed The raw chunk or the transformed chunk
     */
    private function transformChunk(string $chunk)
    {
        if (null === $this->chunkTransformer) {
            return $chunk;
        }

        return \call_user_func($this->chunkTransformer, $chunk);
    }
}
