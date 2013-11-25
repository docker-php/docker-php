<?php

namespace Docker;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Filesystem\Filesystem;

use Exception;

class Context
{
    private $directory;
    private $files = array();
    private $from = 'base';
    private $commands = array();
    private $fs;

    public function __construct(Filesystem $fs = null)
    {
        $this->fs = $fs ?: new Filesystem();
    }

    public function __destruct()
    {
        $this->fs->remove($this->getDirectory());
    }

    public function getDirectory()
    {
        if (null === $this->directory)  {
            $this->directory = sys_get_temp_dir().'/'.md5($this->from.serialize($this->commands));
            $this->fs->mkdir($this->directory);
        }

        return $this->directory;
    }

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

    public function from($from)
    {
        $this->from = $from;
    }

    public function add($path, $content)
    {
        $this->commands[] = ['type' => 'ADD', 'path' => $path, 'content' => $content];
    }

    public function run($command)
    {
        $this->commands[] = ['type' => 'RUN', 'command' => $command];
    }

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

    public function toStream()
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->toTar());
        rewind($stream);

        return $stream;
    }
}