<?php

declare(strict_types=1);

namespace Docker\Context;

use Symfony\Component\Filesystem\Filesystem;

class ContextBuilder
{
    /**
     * @var array
     */
    private $commands = [];

    /**
     * @var array
     */
    private $files = [];

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $entrypoint;

    /**
     * @param \Symfony\Component\Filesystem\Filesystem
     */
    public function __construct(Filesystem $fs = null)
    {
        $this->fs = $fs ?: new Filesystem();
        $this->format = Context::FORMAT_STREAM;
    }

    /**
     * Sets the format of the Context output.
     *
     * @param string $format
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Add a FROM instruction of Dockerfile.
     *
     * @param string $from From which image we start
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function from($from)
    {
        $this->commands[] = ['type' => 'FROM', 'image' => $from];

        return $this;
    }

    /**
     * Set the CMD instruction in the Dockerfile.
     *
     * @param string $command Command to execute
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function command($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Set the ENTRYPOINT instruction in the Dockerfile.
     *
     * @param string $entrypoint The entrypoint
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function entrypoint($entrypoint)
    {
        $this->entrypoint = $entrypoint;

        return $this;
    }

    /**
     * Add an ADD instruction to Dockerfile.
     *
     * @param string $path    Path wanted on the image
     * @param string $content Content of file
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function add($path, $content)
    {
        $this->commands[] = ['type' => 'ADD', 'path' => $path, 'content' => $content];

        return $this;
    }

    /**
     * Add an ADD instruction to Dockerfile.
     *
     * @param string   $path   Path wanted on the image
     * @param resource $stream stream that contains file content
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function addStream($path, $stream)
    {
        $this->commands[] = ['type' => 'ADDSTREAM', 'path' => $path, 'stream' => $stream];

        return $this;
    }

    /**
     * Add an ADD instruction to Dockerfile.
     *
     * @param string $path Path wanted on the image
     * @param string $file Source file (or directory) name
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function addFile($path, $file)
    {
        $this->commands[] = ['type' => 'ADDFILE', 'path' => $path, 'file' => $file];

        return $this;
    }

    /**
     * Add a RUN instruction to Dockerfile.
     *
     * @param string $command Command to run
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function run($command)
    {
        $this->commands[] = ['type' => 'RUN', 'command' => $command];

        return $this;
    }

    /**
     * Add a ENV instruction to Dockerfile.
     *
     * @param string $name  Name of the environment variable
     * @param string $value Value of the environment variable
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function env($name, $value)
    {
        $this->commands[] = ['type' => 'ENV', 'name' => $name, 'value' => $value];

        return $this;
    }

    /**
     * Add a COPY instruction to Dockerfile.
     *
     * @param string $from Path of folder or file to copy
     * @param string $to   Path of destination
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function copy($from, $to)
    {
        $this->commands[] = ['type' => 'COPY', 'from' => $from, 'to' => $to];

        return $this;
    }

    /**
     * Add a WORKDIR instruction to Dockerfile.
     *
     * @param string $workdir Working directory
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function workdir($workdir)
    {
        $this->commands[] = ['type' => 'WORKDIR', 'workdir' => $workdir];

        return $this;
    }

    /**
     * Add a EXPOSE instruction to Dockerfile.
     *
     * @param int $port Port to expose
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function expose($port)
    {
        $this->commands[] = ['type' => 'EXPOSE', 'port' => $port];

        return $this;
    }

    /**
     * Adds an USER instruction to the Dockerfile.
     *
     * @param string $user User to switch to
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function user($user)
    {
        $this->commands[] = ['type' => 'USER', 'user' => $user];

        return $this;
    }

    /**
     * Adds a VOLUME instruction to the Dockerfile.
     *
     * @param string $volume Volume path to add
     *
     * @return \Docker\Context\ContextBuilder
     */
    public function volume($volume)
    {
        $this->commands[] = ['type' => 'VOLUME', 'volume' => $volume];

        return $this;
    }

    /**
     * Create context given the state of builder.
     *
     * @return \Docker\Context\Context
     */
    public function getContext()
    {
        $directory = \sys_get_temp_dir().'/ctb-'.\microtime();
        $this->fs->mkdir($directory);
        $this->write($directory);

        $result = new Context($directory, $this->format, $this->fs);
        $result->setCleanup(true);

        return $result;
    }

    /**
     * Write docker file and associated files in a directory.
     *
     * @param string $directory Target directory
     *
     * @void
     */
    private function write($directory): void
    {
        $dockerfile = [];
        // Insert a FROM instruction if the file does not start with one.
        if (empty($this->commands) || $this->commands[0]['type'] !== 'FROM') {
            $dockerfile[] = 'FROM base';
        }
        foreach ($this->commands as $command) {
            switch ($command['type']) {
                case 'FROM':
                    $dockerfile[] = 'FROM '.$command['image'];
                    break;
                case 'RUN':
                    $dockerfile[] = 'RUN '.$command['command'];
                    break;
                case 'ADD':
                    $dockerfile[] = 'ADD '.$this->getFile($directory, $command['content']).' '.$command['path'];
                    break;
                case 'ADDFILE':
                    $dockerfile[] = 'ADD '.$this->getFileFromDisk($directory, $command['file']).' '.$command['path'];
                    break;
                case 'ADDSTREAM':
                    $dockerfile[] = 'ADD '.$this->getFileFromStream($directory, $command['stream']).' '.$command['path'];
                    break;
                case 'COPY':
                    $dockerfile[] = 'COPY '.$command['from'].' '.$command['to'];
                    break;
                case 'ENV':
                    $dockerfile[] = 'ENV '.$command['name'].' '.$command['value'];
                    break;
                case 'WORKDIR':
                    $dockerfile[] = 'WORKDIR '.$command['workdir'];
                    break;
                case 'EXPOSE':
                    $dockerfile[] = 'EXPOSE '.$command['port'];
                    break;
                case 'VOLUME':
                    $dockerfile[] = 'VOLUME '.$command['volume'];
                    break;
                case 'USER':
                    $dockerfile[] = 'USER '.$command['user'];
                    break;
            }
        }

        if (!empty($this->entrypoint)) {
            $dockerfile[] = 'ENTRYPOINT '.$this->entrypoint;
        }

        if (!empty($this->command)) {
            $dockerfile[] = 'CMD '.$this->command;
        }

        $this->fs->dumpFile($directory.DIRECTORY_SEPARATOR.'Dockerfile', \implode(PHP_EOL, $dockerfile));
    }

    /**
     * Generate a file in a directory.
     *
     * @param string $directory Targeted directory
     * @param string $content   Content of file
     *
     * @return string Name of file generated
     */
    private function getFile($directory, $content)
    {
        $hash = \md5($content);

        if (!\array_key_exists($hash, $this->files)) {
            $file = \tempnam($directory, '');
            $this->fs->dumpFile($file, $content);
            $this->files[$hash] = \basename($file);
        }

        return $this->files[$hash];
    }

    /**
     * Generated a file in a directory from a stream.
     *
     * @param string   $directory Targeted directory
     * @param resource $stream    Stream containing file contents
     *
     * @return string Name of file generated
     */
    private function getFileFromStream($directory, $stream)
    {
        $file = \tempnam($directory, '');
        $target = \fopen($file, 'w');
        if (0 === \stream_copy_to_stream($stream, $target)) {
            throw new \RuntimeException('Failed to write stream to file');
        }
        \fclose($target);

        return \basename($file);
    }

    /**
     * Generated a file in a directory from an existing file.
     *
     * @param string $directory Targeted directory
     * @param string $source    Path to the source file
     *
     * @return string Name of file generated
     */
    private function getFileFromDisk($directory, $source)
    {
        $hash = 'DISK-'.\md5(\realpath($source));
        if (!\array_key_exists($hash, $this->files)) {
            // Check if source is a directory or a file.
            if (\is_dir($source)) {
                $this->fs->mirror($source, $directory.'/'.$hash, null, ['copy_on_windows' => true]);
            } else {
                $this->fs->copy($source, $directory.'/'.$hash);
            }

            $this->files[$hash] = $hash;
        }

        return $this->files[$hash];
    }
}
