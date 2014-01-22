<?php

namespace Docker\Context;

use Symfony\Component\Filesystem\Filesystem;

class ContextBuilder
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var string
     */
    private $from = 'base';

    /**
     * @var array
     */
    private $commands = array();

    /**
     * @var array
     */
    private $files = array();

    /**
     * @var Symfony\Component\Filesystem\Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $format;

    /**
     * @param Symfony\Component\Filesystem\Filesystem
     */
    public function __construct(Filesystem $fs = null)
    {
        $this->fs = $fs ?: new Filesystem();
    }

    /**
     * Sets the format of the Context output
     * 
     * @param string $format
     * 
     * @return Docker\Context\ContextBuilder
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Set the FROM instruction of Dockerfile
     *
     * @param string $from From which image we start
     *
     * @return Docker\Context\ContextBuilder
     */
    public function from($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Add a ADD instruction to Dockerfile
     *
     * @param string $path    Path wanted on the image
     * @param string $content Content of file
     *
     * @return Docker\Context\ContextBuilder
     */
    public function add($path, $content)
    {
        $this->commands[] = ['type' => 'ADD', 'path' => $path, 'content' => $content];

        return $this;
    }

    /**
     * Add a RUN instruction to Dockerfile
     *
     * @param string $command Command to run
     *
     * @return Docker\Context\ContextBuilder
     */
    public function run($command)
    {
        $this->commands[] = ['type' => 'RUN', 'command' => $command];

        return $this;
    }

    /**
     * Create context given the state of builder
     *
     * @return \Docker\Context\Context
     */
    public function getContext()
    {
        if ($this->directory !== null) {
            $this->cleanDirectory();
        }

        $this->directory = sys_get_temp_dir().'/'.md5($this->from.serialize($this->commands));
        $this->fs->mkdir($this->directory);
        $this->write($this->directory);

        return new Context($this->directory, $this->fs, $this->format);
    }

    /**
     * @void
     */
    public function __destruct()
    {
        $this->cleanDirectory();
    }

    /**
     * Write docker file and associated files in a directory
     *
     * @param string $directory Target directory
     *
     * @void
     */
    private function write($directory)
    {
        $dockerfile = [];
        $dockerfile[] = 'FROM '.$this->from;

        foreach ($this->commands as $command) {
            switch($command['type']) {
                case 'RUN':
                    $dockerfile[] = 'RUN '.$command['command'];
                    break;
                case 'ADD':
                    $dockerfile[] = 'ADD '.$this->getFile($directory, $command['content']).' '.$command['path'];
                    break;
            }
        }

        $this->fs->dumpFile($directory.DIRECTORY_SEPARATOR.'Dockerfile', implode(PHP_EOL, $dockerfile));
    }

    /**
     * Generated a file in a directory
     *
     * @param string $directory Targeted directory
     * @param string $content   Content of file
     *
     * @return string Name of file generated
     */
    private function getFile($directory, $content)
    {
        $hash = md5($content);

        if (!array_key_exists($hash, $this->files)) {
            $file = tempnam($directory, '');
            $this->fs->dumpFile($file, $content);
            $this->files[$hash] = basename($file);
        }

        return $this->files[$hash];
    }

    /**
     * Clean directory generated
     */
    private function cleanDirectory()
    {
        $this->fs->remove($this->directory);
    }
}