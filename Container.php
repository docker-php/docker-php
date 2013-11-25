<?php

namespace Docker;

use Guzzle\Http\Client;

class Container
{
    const STATUS_NEW = 0;

    const STATUS_CREATED = 1;

    const STATUS_STARTED = 2;

    private $id;

    private $config = array();

    private $exitCode = null;

    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    public function exists()
    {
        return strlen($this->id) > 0;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
    }

    public function getExitCode()
    {
        return $this->exitCode;
    }

    public function setEnv(array $env)
    {
        $this->config['Env'] = $env;
    }

    public function setImage($image)
    {
        $this->config['Image'] = $image;
    }

    public function setCmd(array $cmd)
    {
        $this->config['Cmd'] = $cmd;
    }
}