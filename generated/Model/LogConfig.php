<?php

namespace Docker\API\Model;

class LogConfig
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string[]
     */
    protected $config;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string[] $config
     *
     * @return self
     */
    public function setConfig(\ArrayObject $config = null)
    {
        $this->config = $config;

        return $this;
    }
}
