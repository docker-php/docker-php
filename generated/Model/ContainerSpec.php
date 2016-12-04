<?php

namespace Docker\API\Model;

class ContainerSpec
{
    /**
     * @var string
     */
    protected $image;
    /**
     * @var string[]|null
     */
    protected $labels;
    /**
     * @var string[]|null
     */
    protected $command;
    /**
     * @var string[]|null
     */
    protected $args;
    /**
     * @var string[]|null
     */
    protected $env;
    /**
     * @var string
     */
    protected $dir;
    /**
     * @var string
     */
    protected $user;
    /**
     * @var ContainerSpecMount[]|null
     */
    protected $mounts;
    /**
     * @var int
     */
    protected $stopGracePeriod;

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return self
     */
    public function setImage($image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param string[]|null $labels
     *
     * @return self
     */
    public function setLabels($labels = null)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string[]|null $command
     *
     * @return self
     */
    public function setCommand($command = null)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param string[]|null $args
     *
     * @return self
     */
    public function setArgs($args = null)
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param string[]|null $env
     *
     * @return self
     */
    public function setEnv($env = null)
    {
        $this->env = $env;

        return $this;
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param string $dir
     *
     * @return self
     */
    public function setDir($dir = null)
    {
        $this->dir = $dir;

        return $this;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param string $user
     *
     * @return self
     */
    public function setUser($user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return ContainerSpecMount[]|null
     */
    public function getMounts()
    {
        return $this->mounts;
    }

    /**
     * @param ContainerSpecMount[]|null $mounts
     *
     * @return self
     */
    public function setMounts($mounts = null)
    {
        $this->mounts = $mounts;

        return $this;
    }

    /**
     * @return int
     */
    public function getStopGracePeriod()
    {
        return $this->stopGracePeriod;
    }

    /**
     * @param int $stopGracePeriod
     *
     * @return self
     */
    public function setStopGracePeriod($stopGracePeriod = null)
    {
        $this->stopGracePeriod = $stopGracePeriod;

        return $this;
    }
}
