<?php

namespace Docker\API\Model;

class ServiceSpec
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
     * @var TaskSpec
     */
    protected $taskTemplate;
    /**
     * @var ServiceSpecMode
     */
    protected $mode;
    /**
     * @var UpdateConfig
     */
    protected $updateConfig;
    /**
     * @var NetworkAttachmentConfig[]|null
     */
    protected $networks;
    /**
     * @var string
     */
    protected $endpointSpec;

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
     * @return TaskSpec
     */
    public function getTaskTemplate()
    {
        return $this->taskTemplate;
    }

    /**
     * @param TaskSpec $taskTemplate
     *
     * @return self
     */
    public function setTaskTemplate(TaskSpec $taskTemplate = null)
    {
        $this->taskTemplate = $taskTemplate;

        return $this;
    }

    /**
     * @return ServiceSpecMode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param ServiceSpecMode $mode
     *
     * @return self
     */
    public function setMode(ServiceSpecMode $mode = null)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return UpdateConfig
     */
    public function getUpdateConfig()
    {
        return $this->updateConfig;
    }

    /**
     * @param UpdateConfig $updateConfig
     *
     * @return self
     */
    public function setUpdateConfig(UpdateConfig $updateConfig = null)
    {
        $this->updateConfig = $updateConfig;

        return $this;
    }

    /**
     * @return NetworkAttachmentConfig[]|null
     */
    public function getNetworks()
    {
        return $this->networks;
    }

    /**
     * @param NetworkAttachmentConfig[]|null $networks
     *
     * @return self
     */
    public function setNetworks($networks = null)
    {
        $this->networks = $networks;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndpointSpec()
    {
        return $this->endpointSpec;
    }

    /**
     * @param string $endpointSpec
     *
     * @return self
     */
    public function setEndpointSpec($endpointSpec = null)
    {
        $this->endpointSpec = $endpointSpec;

        return $this;
    }
}
