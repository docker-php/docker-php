<?php

namespace Docker\API\Model;

class NetworkAttachment
{
    /**
     * @var SwarmNetwork
     */
    protected $network;
    /**
     * @var string[]|null
     */
    protected $addresses;

    /**
     * @return SwarmNetwork
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * @param SwarmNetwork $network
     *
     * @return self
     */
    public function setNetwork(SwarmNetwork $network = null)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param string[]|null $addresses
     *
     * @return self
     */
    public function setAddresses($addresses = null)
    {
        $this->addresses = $addresses;

        return $this;
    }
}
