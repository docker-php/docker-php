<?php

namespace Docker\API\Model;

class SwarmConfigSpecDispatcher
{
    /**
     * @var int
     */
    protected $heartbeatPeriod;

    /**
     * @return int
     */
    public function getHeartbeatPeriod()
    {
        return $this->heartbeatPeriod;
    }

    /**
     * @param int $heartbeatPeriod
     *
     * @return self
     */
    public function setHeartbeatPeriod($heartbeatPeriod = null)
    {
        $this->heartbeatPeriod = $heartbeatPeriod;

        return $this;
    }
}
