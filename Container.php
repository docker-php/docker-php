<?php

namespace Docker;

use Guzzle\Http\Client;

class Container
{
    const STATUS_NEW = 0;

    const STATUS_CREATED = 1;

    const STATUS_STARTED = 2;

    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $config = array();

    /**
     * @var null|integer
     */
    private $exitCode = null;

    /**
     * @var Docker\Image
     */
    private $image;

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * @return boolean
     */
    public function exists()
    {
        return strlen($this->id) > 0;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $id
     * 
     * @return Docker\Container
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $exitCode
     * 
     * @return Docker\Container
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = (integer) $exitCode;

        return $this;
    }

    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @param integer $memory
     * 
     * @return Docker\Container;
     */
    public function setMemory($memory)
    {
        $this->config['Memory'] = (integer) $memory;

        return $this;
    }

    /**
     * @param array $env
     * 
     * @return Docker\Container
     */
    public function setEnv(array $env)
    {
        $this->config['Env'] = $env;

        return $this;
    }

    /**
     * @param string|Docker\Image $image
     * 
     * @return Docker\Container
     */
    public function setImage($image)
    {
        if ($image instanceof Image) {
            $this->image = $image;
        }

        $this->config['Image'] = (string) $image;

        return $this;
    }

    /**
     * @param array $cmd
     * 
     * @return Docker\Container
     */
    public function setCmd(array $cmd)
    {
        $this->config['Cmd'] = $cmd;
    }
}