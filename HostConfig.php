<?php

namespace Docker;

/**
 * Docker\HostConfig
 */
class HostConfig
{
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