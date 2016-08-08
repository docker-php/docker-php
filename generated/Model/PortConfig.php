<?php

namespace Docker\API\Model;

class PortConfig
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $protocol;
    /**
     * @var int
     */
    protected $targetPort;
    /**
     * @var int
     */
    protected $publishedPort;

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
     * @return int
     */
    public function getTargetPort()
    {
        return $this->targetPort;
    }

    /**
     * @param int $targetPort
     *
     * @return self
     */
    public function setTargetPort($targetPort = null)
    {
        $this->targetPort = $targetPort;

        return $this;
    }

    /**
     * @return int
     */
    public function getPublishedPort()
    {
        return $this->publishedPort;
    }

    /**
     * @param int $publishedPort
     *
     * @return self
     */
    public function setPublishedPort($publishedPort = null)
    {
        $this->publishedPort = $publishedPort;

        return $this;
    }
}
