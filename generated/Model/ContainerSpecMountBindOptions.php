<?php

namespace Docker\API\Model;

class ContainerSpecMountBindOptions
{
    /**
     * @var string
     */
    protected $propagation;

    /**
     * @return string
     */
    public function getPropagation()
    {
        return $this->propagation;
    }

    /**
     * @param string $propagation
     *
     * @return self
     */
    public function setPropagation($propagation = null)
    {
        $this->propagation = $propagation;

        return $this;
    }
}
