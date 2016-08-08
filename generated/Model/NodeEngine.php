<?php

namespace Docker\API\Model;

class NodeEngine
{
    /**
     * @var bool
     */
    protected $engineVersion;
    /**
     * @var string[]|null
     */
    protected $labels;
    /**
     * @var NodePlugin[]
     */
    protected $plugins;

    /**
     * @return bool
     */
    public function getEngineVersion()
    {
        return $this->engineVersion;
    }

    /**
     * @param bool $engineVersion
     *
     * @return self
     */
    public function setEngineVersion($engineVersion = null)
    {
        $this->engineVersion = $engineVersion;

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
     * @return NodePlugin[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * @param NodePlugin[] $plugins
     *
     * @return self
     */
    public function setPlugins(array $plugins = null)
    {
        $this->plugins = $plugins;

        return $this;
    }
}
