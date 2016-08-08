<?php

namespace Docker\API\Model;

class EndpointSpec
{
    /**
     * @var string
     */
    protected $mode;
    /**
     * @var PortConfig[]|null
     */
    protected $ports;

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return self
     */
    public function setMode($mode = null)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return PortConfig[]|null
     */
    public function getPorts()
    {
        return $this->ports;
    }

    /**
     * @param PortConfig[]|null $ports
     *
     * @return self
     */
    public function setPorts($ports = null)
    {
        $this->ports = $ports;

        return $this;
    }
}
