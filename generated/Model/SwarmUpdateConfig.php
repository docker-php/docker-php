<?php

namespace Docker\API\Model;

class SwarmUpdateConfig
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var SwarmConfigSpecOrchestration
     */
    protected $orchestration;
    /**
     * @var SwarmConfigSpecRaft
     */
    protected $raft;
    /**
     * @var SwarmConfigSpecDispatcher
     */
    protected $dispatcher;
    /**
     * @var SwarmConfigSpecCAConfig
     */
    protected $cAConfig;
    /**
     * @var SwarmJoinTokens
     */
    protected $joinTokens;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return SwarmConfigSpecOrchestration
     */
    public function getOrchestration()
    {
        return $this->orchestration;
    }

    /**
     * @param SwarmConfigSpecOrchestration $orchestration
     *
     * @return self
     */
    public function setOrchestration(SwarmConfigSpecOrchestration $orchestration = null)
    {
        $this->orchestration = $orchestration;

        return $this;
    }

    /**
     * @return SwarmConfigSpecRaft
     */
    public function getRaft()
    {
        return $this->raft;
    }

    /**
     * @param SwarmConfigSpecRaft $raft
     *
     * @return self
     */
    public function setRaft(SwarmConfigSpecRaft $raft = null)
    {
        $this->raft = $raft;

        return $this;
    }

    /**
     * @return SwarmConfigSpecDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param SwarmConfigSpecDispatcher $dispatcher
     *
     * @return self
     */
    public function setDispatcher(SwarmConfigSpecDispatcher $dispatcher = null)
    {
        $this->dispatcher = $dispatcher;

        return $this;
    }

    /**
     * @return SwarmConfigSpecCAConfig
     */
    public function getCAConfig()
    {
        return $this->cAConfig;
    }

    /**
     * @param SwarmConfigSpecCAConfig $cAConfig
     *
     * @return self
     */
    public function setCAConfig(SwarmConfigSpecCAConfig $cAConfig = null)
    {
        $this->cAConfig = $cAConfig;

        return $this;
    }

    /**
     * @return SwarmJoinTokens
     */
    public function getJoinTokens()
    {
        return $this->joinTokens;
    }

    /**
     * @param SwarmJoinTokens $joinTokens
     *
     * @return self
     */
    public function setJoinTokens(SwarmJoinTokens $joinTokens = null)
    {
        $this->joinTokens = $joinTokens;

        return $this;
    }
}
