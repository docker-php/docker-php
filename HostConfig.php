<?php

namespace Docker;

class HostConfig
{
    private $ports = array();

    /**
     * @param array $env
     * 
     * @return RunSpec
     */
    public function setEnv(array $env)
    {
        $this->env = $env;
    }
}