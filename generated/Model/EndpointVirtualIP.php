<?php

namespace Docker\API\Model;

class EndpointVirtualIP
{
    /**
     * @var string
     */
    protected $networkID;
    /**
     * @var string
     */
    protected $addr;

    /**
     * @return string
     */
    public function getNetworkID()
    {
        return $this->networkID;
    }

    /**
     * @param string $networkID
     *
     * @return self
     */
    public function setNetworkID($networkID = null)
    {
        $this->networkID = $networkID;

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
