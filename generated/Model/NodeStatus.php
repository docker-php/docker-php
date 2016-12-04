<?php

namespace Docker\API\Model;

class NodeStatus
{
    /**
     * @var string
     */
    protected $state;

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return self
     */
    public function setState($state = null)
    {
        $this->state = $state;

        return $this;
    }
}
