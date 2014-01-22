<?php

namespace Docker\Context;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Docker\Context
 */
class Context implements ContextInterface
{
    const FORMAT_STREAM = 'stream';

    const FORMAT_TAR = 'tar';

    /**
     * @var string
     */
    private $directory;

    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    private $fs;

    /**
     * @var process Tar process
     */
    private $process;

    /**
     * @var stream Tar stream
     */
    private $stream;

    /**
     * @param Symfony\Component\Filesystem\Filesystem
     */
    public function __construct($directory, Filesystem $fs = null, $format = self::FORMAT_STREAM)
    {
        $this->directory = $directory;
        $this->fs = $fs ?: new Filesystem();
        $this->format = $format;
    }

    /**
     * Get directory of Context
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set directory of Context
     *
     * @param string $directory Targeted directory
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
    }

    /**
     * Return content of Dockerfile of this context
     *
     * @return string Content of dockerfile
     */
    public function getDockerfileContent()
    {
        return file_get_contents($this->directory.DIRECTORY_SEPARATOR.'Dockerfile');
    }

    /**
     * @return boolean
     */
    public function isStreamed()
    {
        return $this->format === self::FORMAT_STREAM;
    }

    /**
     * @return ressource|string
     */
    public function read()
    {
        return $this->isStreamed() ? $this->toStream() : $this->toTar();
    }

    /**
     * Return the context as a tar archive
     *
     * @return string Tar content
     */
    public function toTar()
    {
        $process = new Process('/bin/tar c .', $this->directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Return a stream for this context
     *
     * @return stream Stream ressource in memory
     */
    public function toStream()
    {
        if (!is_resource($this->process)) {
            $this->process = proc_open("/bin/tar c .", array(array("pipe", "r"), array("pipe", "w"), array("pipe", "w")), $pipes, $this->directory);
            $this->stream  = $pipes[1];
        }

        return $this->stream;
    }

    public function __destruct()
    {
        if (is_resource($this->process)) {
            proc_close($this->process);
        }

        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }
}