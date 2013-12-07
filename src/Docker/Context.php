<?php

namespace Docker;

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
     * @var array
     */
    private $files = array();

    /**
     * @var string
     */
    private $from = 'base';

    /**
     * @var array
     */
    private $commands = array();

    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    private $fs;

    /**
     * @param Symfony\Component\Filesystem\Filesystem
     */
    public function __construct(Filesystem $fs = null)
    {
        $this->fs = $fs ?: new Filesystem();
    }

    /**
     * @void
     */
    public function __destruct()
    {
        $this->fs->remove($this->getDirectory());
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        if (null === $this->directory)  {
            $this->directory = sys_get_temp_dir().'/'.md5($this->from.serialize($this->commands));
            $this->fs->mkdir($this->directory);
        }

        return $this->directory;
    }

    /**
     * @param string $content
     * 
     * @return string
     */
    public function getFile($content)
    {
        $hash = md5($content);

        if (!array_key_exists($hash, $this->files)) {
            $file = tempnam($this->getDirectory(), '');
            $this->fs->dumpFile($file, $content);
            $this->files[$hash] = basename($file);
        }

        return $this->files[$hash];
    }

    /**
     * @param string $from
     * 
     * @return Docker\Context
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @param string $path
     * @param string $content
     * 
     * @return Docker\Context
     */
    public function add($path, $content)
    {
        $this->commands[] = ['type' => 'ADD', 'path' => $path, 'content' => $content];

        return $this;
    }

    /**
     * @param string $command
     * 
     * @return Docker\Context
     */
    public function run($command)
    {
        $this->commands[] = ['type' => 'RUN', 'command' => $command];

        return $this;
    }

    /**
     * @return string
     */
    public function write()
    {
        $dockerfile = [];
        $dockerfile[] = 'FROM '.$this->from;

        foreach ($this->commands as $command) {
            switch($command['type']) {
                case 'RUN':
                    $dockerfile[] = 'RUN '.$command['command'];
                    break;
                case 'ADD':
                    $dockerfile[] = 'ADD '.$this->getFile($command['content']).' '.$command['path'];
            }
        }

        $this->fs->dumpFile($this->getDirectory().'/Dockerfile', implode(PHP_EOL, $dockerfile));

        return $this->getDirectory();
    }

    /**
     * @return string
     */
    public function getDockerfileAsString()
    {
        $this->write();
        
        return file_get_contents($this->getDirectory().'/Dockerfile');
    }

    /**
     * @return string
     */
    public function toTar()
    {
        $directory = $this->write();

        $process = new Process('/bin/tar c .', $directory);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * @return stream
     */
    public function toStream()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->toTar());
        rewind($stream);

        return $stream;
    }
}