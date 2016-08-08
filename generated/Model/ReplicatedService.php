<?php

namespace Docker\API\Model;

class ReplicatedService
{
    /**
     * @var int
     */
    protected $replicas;

    /**
     * @return int
     */
    public function getReplicas()
    {
        return $this->replicas;
    }

    /**
     * @param int $replicas
     *
     * @return self
     */
    public function setReplicas($replicas = null)
    {
        $this->replicas = $replicas;

        return $this;
    }
}
