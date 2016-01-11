<?php

namespace Docker\API\Model;

class ContainerConnect
{
    /**
     * @var string
     */
    protected $container;

    /**
     * @return string
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $container
     *
     * @return self
     */
    public function setContainer($container = null)
    {
        $this->container = $container;

        return $this;
    }
}
