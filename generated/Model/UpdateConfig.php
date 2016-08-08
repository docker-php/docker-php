<?php

namespace Docker\API\Model;

class UpdateConfig
{
    /**
     * @var int
     */
    protected $parallelism;
    /**
     * @var int
     */
    protected $delay;
    /**
     * @var string
     */
    protected $failureAction;

    /**
     * @return int
     */
    public function getParallelism()
    {
        return $this->parallelism;
    }

    /**
     * @param int $parallelism
     *
     * @return self
     */
    public function setParallelism($parallelism = null)
    {
        $this->parallelism = $parallelism;

        return $this;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param int $delay
     *
     * @return self
     */
    public function setDelay($delay = null)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * @return string
     */
    public function getFailureAction()
    {
        return $this->failureAction;
    }

    /**
     * @param string $failureAction
     *
     * @return self
     */
    public function setFailureAction($failureAction = null)
    {
        $this->failureAction = $failureAction;

        return $this;
    }
}
