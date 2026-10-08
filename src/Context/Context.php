<?php

declare(strict_types=1);

namespace Docker\Context;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Docker\Context\Context.
 */
class Context implements ContextInterface
{
    public const FORMAT_STREAM = 'stream';

    public const FORMAT_TAR = 'tar';

    /**
     * @var bool Whether to remove the context directory
     */
    private $cleanup = false;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var process Tar process
     */
    private $process;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var resource Tar stream
     */
    private $stream;

    /**
     * @var string Format of the context (stream or tar)
     */
    private $format = self::FORMAT_STREAM;

    private bool $applyDockerignore = false;

    /** Temporary NUL-separated list of the paths to archive. */
    private ?string $pathList = null;

    /**
     * @param string     $directory Directory of context
     * @param string     $format    Format to use when sending the call (stream or tar: string)
     * @param Filesystem $fs        filesystem object for cleaning the context directory on destruction
     */
    public function __construct($directory, $format = self::FORMAT_STREAM, ?Filesystem $fs = null)
    {
        $this->directory = $directory;
        $this->format = $format;
        $this->fs = $fs ?? new Filesystem();
    }

    /**
     * Get directory of Context.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set directory of Context.
     *
     * @param string $directory Targeted directory
     */
    public function setDirectory($directory): void
    {
        $this->directory = $directory;
    }

    /**
     * Return content of Dockerfile of this context.
     *
     * @return string Content of dockerfile
     */
    public function getDockerfileContent()
    {
        return file_get_contents($this->directory.\DIRECTORY_SEPARATOR.'Dockerfile');
    }

    /**
     * Leave out paths matched by the context's .dockerignore file, as `docker build` does.
     *
     * Without this, the whole directory is archived and a .dockerignore file
     * triggers a deprecation notice. Applying it becomes the default in 4.0.
     */
    public function applyDockerignore(bool $enabled = true): static
    {
        $this->applyDockerignore = $enabled;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStreamed()
    {
        return self::FORMAT_STREAM === $this->format;
    }

    /**
     * @return resource|string
     */
    public function read()
    {
        return $this->isStreamed() ? $this->toStream() : $this->toTar();
    }

    /**
     * Return the context as a tar archive.
     *
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     *
     * @return string Tar content
     */
    public function toTar()
    {
        $process = new Process($this->tarCommand(), $this->directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Return a stream for this context.
     *
     * @return resource Stream resource in memory
     */
    public function toStream()
    {
        if (!\is_resource($this->process)) {
            $this->process = proc_open($this->tarCommand(), [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes, $this->directory);
            $this->stream = $pipes[1];
        }

        return $this->stream;
    }

    public function __destruct()
    {
        if (\is_resource($this->stream)) {
            fclose($this->stream);
        }

        if (\is_resource($this->process)) {
            proc_close($this->process);
        }

        if (null !== $this->pathList) {
            $this->fs->remove($this->pathList);
        }

        if ($this->cleanup) {
            $this->fs->remove($this->directory);
        }
    }

    /**
     * @return list<string>
     */
    private function tarCommand(): array
    {
        $dockerignore = $this->directory.\DIRECTORY_SEPARATOR.'.dockerignore';
        if (!is_file($dockerignore)) {
            return ['/usr/bin/env', 'tar', '-c', '.'];
        }

        if (!$this->applyDockerignore) {
            trigger_deprecation(
                'docker-php/docker-php',
                '3.3',
                'The build context %s has a .dockerignore file that is not applied; from 4.0 it will be. Call Context::applyDockerignore() to opt in now.',
                $this->directory
            );

            return ['/usr/bin/env', 'tar', '-c', '.'];
        }

        // Write the list to a file rather than tar's stdin, so tar can stream
        // the archive without waiting for the whole list to be read.
        if (null === $this->pathList) {
            $paths = Dockerignore::fromFile($dockerignore)->paths($this->directory);
            $this->pathList = $this->fs->tempnam(sys_get_temp_dir(), 'docker-context-');
            // "./" keeps names that start with "-" from being read as options.
            $this->fs->dumpFile($this->pathList, implode('', array_map(static fn (string $path): string => './'.$path."\0", $paths)));
        }

        return ['/usr/bin/env', 'tar', '-c', '--no-recursion', '--null', '-T', $this->pathList];
    }

    /**
     * @param bool $value whether to remove the context directory
     */
    public function setCleanup(bool $value): void
    {
        $this->cleanup = $value;
    }
}
