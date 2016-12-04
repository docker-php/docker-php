<?php

namespace Docker\API\Model;

class SwarmIPAMOptions
{
    /**
     * @var Driver
     */
    protected $driver;
    /**
     * @var IPAMConfig[]|null
     */
    protected $configs;

    /**
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param Driver $driver
     *
     * @return self
     */
    public function setDriver(Driver $driver = null)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return IPAMConfig[]|null
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @param IPAMConfig[]|null $configs
     *
     * @return self
     */
    public function setConfigs($configs = null)
    {
        $this->configs = $configs;

        return $this;
    }
}
