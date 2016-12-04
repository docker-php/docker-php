<?php

namespace Docker\API\Model;

class SwarmJoinConfig
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
     * @var string[]|null
     */
    protected $remoteAddrs;
    /**
     * @var string
     */
    protected $joinToken;

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
     * @return string[]|null
     */
    public function getRemoteAddrs()
    {
        return $this->remoteAddrs;
    }

    /**
     * @param string[]|null $remoteAddrs
     *
     * @return self
     */
    public function setRemoteAddrs($remoteAddrs = null)
    {
        $this->remoteAddrs = $remoteAddrs;

        return $this;
    }

    /**
     * @return string
     */
    public function getJoinToken()
    {
        return $this->joinToken;
    }

    /**
     * @param string $joinToken
     *
     * @return self
     */
    public function setJoinToken($joinToken = null)
    {
        $this->joinToken = $joinToken;

        return $this;
    }
}
