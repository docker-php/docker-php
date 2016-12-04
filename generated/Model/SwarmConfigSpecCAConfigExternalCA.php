<?php

namespace Docker\API\Model;

class SwarmConfigSpecCAConfigExternalCA
{
    /**
     * @var string
     */
    protected $protocol;
    /**
     * @var string
     */
    protected $uRL;
    /**
     * @var string[]|null
     */
    protected $options;

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @param string $protocol
     *
     * @return self
     */
    public function setProtocol($protocol = null)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @return string
     */
    public function getURL()
    {
        return $this->uRL;
    }

    /**
     * @param string $uRL
     *
     * @return self
     */
    public function setURL($uRL = null)
    {
        $this->uRL = $uRL;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string[]|null $options
     *
     * @return self
     */
    public function setOptions($options = null)
    {
        $this->options = $options;

        return $this;
    }
}
