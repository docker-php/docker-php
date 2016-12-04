<?php

namespace Docker\API\Model;

class Endpoint
{
    /**
     * @var EndpointSpec
     */
    protected $spec;
    /**
     * @var PortConfig[]|null
     */
    protected $exposedPorts;
    /**
     * @var EndpointVirtualIP[]|null
     */
    protected $virtualIPs;

    /**
     * @return EndpointSpec
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param EndpointSpec $spec
     *
     * @return self
     */
    public function setSpec(EndpointSpec $spec = null)
    {
        $this->spec = $spec;

        return $this;
    }

    /**
     * @return PortConfig[]|null
     */
    public function getExposedPorts()
    {
        return $this->exposedPorts;
    }

    /**
     * @param PortConfig[]|null $exposedPorts
     *
     * @return self
     */
    public function setExposedPorts($exposedPorts = null)
    {
        $this->exposedPorts = $exposedPorts;

        return $this;
    }

    /**
     * @return EndpointVirtualIP[]|null
     */
    public function getVirtualIPs()
    {
        return $this->virtualIPs;
    }

    /**
     * @param EndpointVirtualIP[]|null $virtualIPs
     *
     * @return self
     */
    public function setVirtualIPs($virtualIPs = null)
    {
        $this->virtualIPs = $virtualIPs;

        return $this;
    }
}
