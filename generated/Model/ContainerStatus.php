<?php

namespace Docker\API\Model;

class ContainerStatus
{
    /**
     * @var string
     */
    protected $containerID;
    /**
     * @var int
     */
    protected $pID;
    /**
     * @var int
     */
    protected $exitCode;

    /**
     * @return string
     */
    public function getContainerID()
    {
        return $this->containerID;
    }

    /**
     * @param string $containerID
     *
     * @return self
     */
    public function setContainerID($containerID = null)
    {
        $this->containerID = $containerID;

        return $this;
    }

    /**
     * @return int
     */
    public function getPID()
    {
        return $this->pID;
    }

    /**
     * @param int $pID
     *
     * @return self
     */
    public function setPID($pID = null)
    {
        $this->pID = $pID;

        return $this;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @param int $exitCode
     *
     * @return self
     */
    public function setExitCode($exitCode = null)
    {
        $this->exitCode = $exitCode;

        return $this;
    }
}
