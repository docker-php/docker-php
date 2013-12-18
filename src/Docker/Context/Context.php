<?php

namespace Docker\Context;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Docker\Context
 */
class Context
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    private $fs;

    /**
     * @param Symfony\Component\Filesystem\Filesystem
     */
    public function __construct($directory, Filesystem $fs = null)
    {
        $this->directory = $directory;
        $this->fs = $fs ?: new Filesystem();
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
     * Return the context as a tar archive
     *
     * @return string Tar content
     */
    public function toTar()
    {
        $filename = tempnam(sys_get_temp_dir(), "docker-build-");

        if (file_exists($filename)) {
            unlink($filename);
        }

        $filename = $filename.".tar";
        $phar     = new \PharData($filename);

        $phar->buildFromDirectory($this->directory);
        $tarContent = file_get_contents($filename);

        unset($phar);
        unlink($filename);

        return $tarContent;
    }

    /**
     * Return a stream for this context
     *
     * @return stream Stream ressource in memory
     */
    public function toStream()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->toTar());
        rewind($stream);

        return $stream;
    }
}