<?php

namespace Docker;

/**
 * Docker\HostConfig
 */
class HostConfig
{
    /**
     * @var array
     */
    private $env;

    /**
     * @param array $env
     * 
     * @return \Docker\HostConfig
     */
    public function setEnv(array $env)
    {
        $this->env = $env;

        return $this;
    }
}