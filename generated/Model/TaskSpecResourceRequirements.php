<?php

namespace Docker\API\Model;

class TaskSpecResourceRequirements
{
    /**
     * @var NodeResources
     */
    protected $limits;
    /**
     * @var NodeResources
     */
    protected $reservations;

    /**
     * @return NodeResources
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * @param NodeResources $limits
     *
     * @return self
     */
    public function setLimits(NodeResources $limits = null)
    {
        $this->limits = $limits;

        return $this;
    }

    /**
     * @return NodeResources
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * @param NodeResources $reservations
     *
     * @return self
     */
    public function setReservations(NodeResources $reservations = null)
    {
        $this->reservations = $reservations;

        return $this;
    }
}
