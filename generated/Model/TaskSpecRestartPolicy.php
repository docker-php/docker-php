<?php

namespace Docker\API\Model;

class TaskSpecRestartPolicy
{
    /**
     * @var string
     */
    protected $condition;
    /**
     * @var int
     */
    protected $delay;
    /**
     * @var int
     */
    protected $maxAttempts;
    /**
     * @var int
     */
    protected $window;

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     *
     * @return self
     */
    public function setCondition($condition = null)
    {
        $this->condition = $condition;

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
     * @return int
     */
    public function getMaxAttempts()
    {
        return $this->maxAttempts;
    }

    /**
     * @param int $maxAttempts
     *
     * @return self
     */
    public function setMaxAttempts($maxAttempts = null)
    {
        $this->maxAttempts = $maxAttempts;

        return $this;
    }

    /**
     * @return int
     */
    public function getWindow()
    {
        return $this->window;
    }

    /**
     * @param int $window
     *
     * @return self
     */
    public function setWindow($window = null)
    {
        $this->window = $window;

        return $this;
    }
}
