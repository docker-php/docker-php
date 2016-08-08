<?php

namespace Docker\API\Model;

class TaskSpec
{
    /**
     * @var ContainerSpec
     */
    protected $containerSpec;
    /**
     * @var TaskSpecResourceRequirements
     */
    protected $resources;
    /**
     * @var TaskSpecRestartPolicy
     */
    protected $restartPolicy;
    /**
     * @var TaskSpecPlacement
     */
    protected $placement;

    /**
     * @return ContainerSpec
     */
    public function getContainerSpec()
    {
        return $this->containerSpec;
    }

    /**
     * @param ContainerSpec $containerSpec
     *
     * @return self
     */
    public function setContainerSpec(ContainerSpec $containerSpec = null)
    {
        $this->containerSpec = $containerSpec;

        return $this;
    }

    /**
     * @return TaskSpecResourceRequirements
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * @param TaskSpecResourceRequirements $resources
     *
     * @return self
     */
    public function setResources(TaskSpecResourceRequirements $resources = null)
    {
        $this->resources = $resources;

        return $this;
    }

    /**
     * @return TaskSpecRestartPolicy
     */
    public function getRestartPolicy()
    {
        return $this->restartPolicy;
    }

    /**
     * @param TaskSpecRestartPolicy $restartPolicy
     *
     * @return self
     */
    public function setRestartPolicy(TaskSpecRestartPolicy $restartPolicy = null)
    {
        $this->restartPolicy = $restartPolicy;

        return $this;
    }

    /**
     * @return TaskSpecPlacement
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param TaskSpecPlacement $placement
     *
     * @return self
     */
    public function setPlacement(TaskSpecPlacement $placement = null)
    {
        $this->placement = $placement;

        return $this;
    }
}
