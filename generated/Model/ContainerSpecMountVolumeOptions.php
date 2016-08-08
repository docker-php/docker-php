<?php

namespace Docker\API\Model;

class ContainerSpecMountVolumeOptions
{
    /**
     * @var bool
     */
    protected $noCopy;
    /**
     * @var string[]|null
     */
    protected $labels;
    /**
     * @var Driver
     */
    protected $driverConfig;

    /**
     * @return bool
     */
    public function getNoCopy()
    {
        return $this->noCopy;
    }

    /**
     * @param bool $noCopy
     *
     * @return self
     */
    public function setNoCopy($noCopy = null)
    {
        $this->noCopy = $noCopy;

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
    public function getDriverConfig()
    {
        return $this->driverConfig;
    }

    /**
     * @param Driver $driverConfig
     *
     * @return self
     */
    public function setDriverConfig(Driver $driverConfig = null)
    {
        $this->driverConfig = $driverConfig;

        return $this;
    }
}
