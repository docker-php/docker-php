<?php

namespace Docker\API\Model;

class NodeResources
{
    /**
     * @var int
     */
    protected $nanoCPUs;
    /**
     * @var int
     */
    protected $memoryBytes;

    /**
     * @return int
     */
    public function getNanoCPUs()
    {
        return $this->nanoCPUs;
    }

    /**
     * @param int $nanoCPUs
     *
     * @return self
     */
    public function setNanoCPUs($nanoCPUs = null)
    {
        $this->nanoCPUs = $nanoCPUs;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemoryBytes()
    {
        return $this->memoryBytes;
    }

    /**
     * @param int $memoryBytes
     *
     * @return self
     */
    public function setMemoryBytes($memoryBytes = null)
    {
        $this->memoryBytes = $memoryBytes;

        return $this;
    }
}
