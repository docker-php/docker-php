<?php

namespace Docker\API\Model;

class SwarmNetwork
{
    /**
     * @var string
     */
    protected $iD;
    /**
     * @var NodeVersion
     */
    protected $version;
    /**
     * @var \DateTime
     */
    protected $createdAt;
    /**
     * @var \DateTime
     */
    protected $updatedAt;
    /**
     * @var SwarmNetworkSpec
     */
    protected $spec;
    /**
     * @var Driver
     */
    protected $driverState;
    /**
     * @var SwarmIPAMOptions
     */
    protected $iPAM;

    /**
     * @return string
     */
    public function getID()
    {
        return $this->iD;
    }

    /**
     * @param string $iD
     *
     * @return self
     */
    public function setID($iD = null)
    {
        $this->iD = $iD;

        return $this;
    }

    /**
     * @return NodeVersion
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param NodeVersion $version
     *
     * @return self
     */
    public function setVersion(NodeVersion $version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return SwarmNetworkSpec
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param SwarmNetworkSpec $spec
     *
     * @return self
     */
    public function setSpec(SwarmNetworkSpec $spec = null)
    {
        $this->spec = $spec;

        return $this;
    }

    /**
     * @return Driver
     */
    public function getDriverState()
    {
        return $this->driverState;
    }

    /**
     * @param Driver $driverState
     *
     * @return self
     */
    public function setDriverState(Driver $driverState = null)
    {
        $this->driverState = $driverState;

        return $this;
    }

    /**
     * @return SwarmIPAMOptions
     */
    public function getIPAM()
    {
        return $this->iPAM;
    }

    /**
     * @param SwarmIPAMOptions $iPAM
     *
     * @return self
     */
    public function setIPAM(SwarmIPAMOptions $iPAM = null)
    {
        $this->iPAM = $iPAM;

        return $this;
    }
}
