<?php

namespace Docker\API\Model;

class ContainerSpecMount
{
    /**
     * @var string
     */
    protected $type;
    /**
     * @var string
     */
    protected $source;
    /**
     * @var string
     */
    protected $target;
    /**
     * @var bool
     */
    protected $readOnly;
    /**
     * @var ContainerSpecMountBindOptions
     */
    protected $bindOptions;
    /**
     * @var ContainerSpecMountVolumeOptions
     */
    protected $volumeOptions;

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
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $source
     *
     * @return self
     */
    public function setSource($source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     *
     * @return self
     */
    public function setTarget($target = null)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @param bool $readOnly
     *
     * @return self
     */
    public function setReadOnly($readOnly = null)
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    /**
     * @return ContainerSpecMountBindOptions
     */
    public function getBindOptions()
    {
        return $this->bindOptions;
    }

    /**
     * @param ContainerSpecMountBindOptions $bindOptions
     *
     * @return self
     */
    public function setBindOptions(ContainerSpecMountBindOptions $bindOptions = null)
    {
        $this->bindOptions = $bindOptions;

        return $this;
    }

    /**
     * @return ContainerSpecMountVolumeOptions
     */
    public function getVolumeOptions()
    {
        return $this->volumeOptions;
    }

    /**
     * @param ContainerSpecMountVolumeOptions $volumeOptions
     *
     * @return self
     */
    public function setVolumeOptions(ContainerSpecMountVolumeOptions $volumeOptions = null)
    {
        $this->volumeOptions = $volumeOptions;

        return $this;
    }
}
