<?php

namespace Docker\API\Model;

class NodeDescription
{
    /**
     * @var string
     */
    protected $hostname;
    /**
     * @var NodePlatform
     */
    protected $platform;
    /**
     * @var NodeResources
     */
    protected $resources;
    /**
     * @var NodeEngine
     */
    protected $engine;

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param string $hostname
     *
     * @return self
     */
    public function setHostname($hostname = null)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return NodePlatform
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param NodePlatform $platform
     *
     * @return self
     */
    public function setPlatform(NodePlatform $platform = null)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * @return NodeResources
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @param NodeResources $resources
     *
     * @return self
     */
    public function setResources(NodeResources $resources = null)
    {
        $this->resources = $resources;

        return $this;
    }

    /**
     * @return NodeEngine
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * @param NodeEngine $engine
     *
     * @return self
     */
    public function setEngine(NodeEngine $engine = null)
    {
        $this->engine = $engine;

        return $this;
    }
}
