<?php

namespace Docker\API\Model;

class SwarmNetworkSpec
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string[]|null
     */
    protected $labels;
    /**
     * @var Driver
     */
    protected $driverConfiguration;
    /**
     * @var bool
     */
    protected $iPv6Enabled;
    /**
     * @var bool
     */
    protected $internal;
    /**
     * @var SwarmIPAMOptions
     */
    protected $iPAM;

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
     * @return string[]|null
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param string[]|null $labels
     *
     * @return self
     */
    public function setLabels($labels = null)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return Driver
     */
    public function getDriverConfiguration()
    {
        return $this->driverConfiguration;
    }

    /**
     * @param Driver $driverConfiguration
     *
     * @return self
     */
    public function setDriverConfiguration(Driver $driverConfiguration = null)
    {
        $this->driverConfiguration = $driverConfiguration;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIPv6Enabled()
    {
        return $this->iPv6Enabled;
    }

    /**
     * @param bool $iPv6Enabled
     *
     * @return self
     */
    public function setIPv6Enabled($iPv6Enabled = null)
    {
        $this->iPv6Enabled = $iPv6Enabled;

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
