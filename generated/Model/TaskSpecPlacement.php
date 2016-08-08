<?php

namespace Docker\API\Model;

class TaskSpecPlacement
{
    /**
     * @var string[]|null
     */
    protected $constraints;

    /**
     * @return string[]|null
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * @param string[]|null $constraints
     *
     * @return self
     */
    public function setConstraints($constraints = null)
    {
        $this->constraints = $constraints;

        return $this;
    }
}
