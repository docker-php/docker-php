<?php

namespace Docker\API\Model;

class NodeManagerStatus
{
    /**
     * @var bool
     */
    protected $leader;
    /**
     * @var string
     */
    protected $reachability;
    /**
     * @var string
     */
    protected $addr;

    /**
     * @return bool
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * @param bool $leader
     *
     * @return self
     */
    public function setLeader($leader = null)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * @return string
     */
    public function getReachability()
    {
        return $this->reachability;
    }

    /**
     * @param string $reachability
     *
     * @return self
     */
    public function setReachability($reachability = null)
    {
        $this->reachability = $reachability;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddr()
    {
        return $this->addr;
    }

    /**
     * @param string $addr
     *
     * @return self
     */
    public function setAddr($addr = null)
    {
        $this->addr = $addr;

        return $this;
    }
}
