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
    private $ports = array();

    /**
     * @param array $env
     * 
     * @return Docker\HostConfig
     */
    public function setEnv(array $env)
    {
        $this->env = $env;

        return $this;
    }
}