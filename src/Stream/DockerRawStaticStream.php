<?php

namespace Docker\Stream;

use Psr\Http\Message\StreamInterface;

class DockerRawStaticStream
{
    /** @var string Content of the standard input */
    protected $stdIn;

    /** @var string Content of the standard output */
    protected $stdOut;

    /** @var string Content of the error output */
    protected $stdErr;

    public function __construct(StreamInterface $stream)
    {
        $dockerRawStream = new DockerRawStream($stream);

        $dockerRawStream->onStdin(function($stdIn) {
            $this->stdIn = $stdIn;
        });
        $dockerRawStream->onStdout(function($stdOut) {
            $this->stdOut = $stdOut;
        });
        $dockerRawStream->onStderr(function($stdErr) {
            $this->stdErr = $stdErr;
        });

        $dockerRawStream->wait();
    }

    /**
     * @return string
     */
    public function getStdIn() {
        return $this->stdIn;
    }

    /**
     * @return string
     */
    public function getStdOut() {
        return $this->stdOut;
    }

    /**
     * @return string
     */
    public function getStdErr() {
        return $this->stdErr;
    }
}
