<?php

namespace Docker\API\Model;

class ServiceSpecMode
{
    /**
     * @var ReplicatedService
     */
    protected $replicated;
    /**
     * @var GlobalService
     */
    protected $global;

    /**
     * @return ReplicatedService
     */
    public function getReplicated()
    {
        return $this->replicated;
    }

    /**
     * @param ReplicatedService $replicated
     *
     * @return self
     */
    public function setReplicated(ReplicatedService $replicated = null)
    {
        $this->replicated = $replicated;

        return $this;
    }

    /**
     * @return GlobalService
     */
    public function getGlobal()
    {
        return $this->global;
    }

    /**
     * @param GlobalService $global
     *
     * @return self
     */
    public function setGlobal(GlobalService $global = null)
    {
        $this->global = $global;

        return $this;
    }
}
