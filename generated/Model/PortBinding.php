<?php

namespace Docker\API\Model;

class PortBinding
{
    /**
     * @var string
     */
    protected $hostPort;

    /**
     * @return string
     */
    public function getHostPort()
    {
        return $this->hostPort;
    }

    /**
     * @param string $hostPort
     *
     * @return self
     */
    public function setHostPort($hostPort = null)
    {
        $this->hostPort = $hostPort;

        return $this;
    }
}
