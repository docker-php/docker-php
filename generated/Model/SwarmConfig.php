<?php

namespace Docker\API\Model;

class SwarmConfig
{
    /**
     * @var string
     */
    protected $listenAddr;
    /**
     * @var string
     */
    protected $advertiseAddr;
    /**
     * @var bool
     */
    protected $forceNewCluster;
    /**
     * @var SwarmConfigSpec
     */
    protected $spec;

    /**
     * @return string
     */
    public function getListenAddr()
    {
        return $this->listenAddr;
    }

    /**
     * @param string $listenAddr
     *
     * @return self
     */
    public function setListenAddr($listenAddr = null)
    {
        $this->listenAddr = $listenAddr;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdvertiseAddr()
    {
        return $this->advertiseAddr;
    }

    /**
     * @param string $advertiseAddr
     *
     * @return self
     */
    public function setAdvertiseAddr($advertiseAddr = null)
    {
        $this->advertiseAddr = $advertiseAddr;

        return $this;
    }

    /**
     * @return bool
     */
    public function getForceNewCluster()
    {
        return $this->forceNewCluster;
    }

    /**
     * @param bool $forceNewCluster
     *
     * @return self
     */
    public function setForceNewCluster($forceNewCluster = null)
    {
        $this->forceNewCluster = $forceNewCluster;

        return $this;
    }

    /**
     * @return SwarmConfigSpec
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param SwarmConfigSpec $spec
     *
     * @return self
     */
    public function setSpec(SwarmConfigSpec $spec = null)
    {
        $this->spec = $spec;

        return $this;
    }
}
