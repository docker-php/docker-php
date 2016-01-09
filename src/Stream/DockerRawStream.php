<?php

namespace Docker\Stream;

use Psr\Http\Message\StreamInterface;

class DockerRawStream
{
    const HEADER = 'application/vnd.docker.raw-stream';

    /** @var StreamInterface Stream for the response */
    protected $stream;

    /** @var callable[] A list of callable to call when there is a stdin output */
    protected $onStdinCallables = [];

    /** @var callable[] A list of callable to call when there is a stdout output */
    protected $onStdoutCallables = [];

    /** @var callable[] A list of callable to call when there is a stderr output */
    protected $onStderrCallables = [];

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Add a callable to read stdin
     *
     * @param callable $callback
     */
    public function onStdin(callable $callback)
    {
        $this->onStdinCallables[] = $callback;
    }

    /**
     * Add a callable to read stdout
     *
     * @param callable $callback
     */
    public function onStdout(callable $callback)
    {
        $this->onStdoutCallables[] = $callback;
    }

    /**
     * Add a callable to read stderr
     *
     * @param callable $callback
     */
    public function onStderr(callable $callback)
    {
        $this->onStderrCallables[] = $callback;
    }

    /**
     * Read a frame in the stream
     */
    protected function readFrame()
    {
        $header  = $this->stream->read(8);
        $decoded = unpack('C1type/C3/N1size', $header);
        $output  = $this->stream->read($decoded['size']);
        $callbackList = [];

        if ($decoded['type'] == 0) {
            $callbackList = $this->onStdinCallables;
        }

        if ($decoded['type'] == 1) {
            $callbackList = $this->onStdoutCallables;
        }

        if ($decoded['type'] == 2) {
            $callbackList = $this->onStderrCallables;
        }

        foreach ($callbackList as $callback) {
            $callback($output);
        }
    }

    /**
     * Wait for stream to finish and call callables if defined
     */
    public function wait()
    {
        while (!$this->stream->eof()) {
            $this->readFrame();
        }
    }
}
