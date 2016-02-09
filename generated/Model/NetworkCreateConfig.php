<?php

namespace Docker\API\Model;

class NetworkCreateConfig
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $driver;
    /**
     * @var IPAM
     */
    protected $iPAM;
    /**
     * @var bool
     */
    protected $internal;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param string $driver
     *
     * @return self
     */
    public function setDriver($driver = null)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return IPAM
     */
    public function getIPAM()
    {
        return $this->iPAM;
    }

    /**
     * @param IPAM $iPAM
     *
     * @return self
     */
    public function setIPAM(IPAM $iPAM = null)
    {
        $this->iPAM = $iPAM;

        return $this;
    }

    /**
     * @return bool
     */
    public function getInternal()
    {
        return $this->internal;
    }

    /**
     * @param bool $internal
     *
     * @return self
     */
    public function setInternal($internal = null)
    {
        $this->internal = $internal;

        return $this;
    }
}
