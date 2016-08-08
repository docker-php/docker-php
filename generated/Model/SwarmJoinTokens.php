<?php

namespace Docker\API\Model;

class SwarmJoinTokens
{
    /**
     * @var string
     */
    protected $worker;
    /**
     * @var string
     */
    protected $manager;

    /**
     * @return string
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param string $worker
     *
     * @return self
     */
    public function setWorker($worker = null)
    {
        $this->worker = $worker;

        return $this;
    }

    /**
     * @return string
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @param string $manager
     *
     * @return self
     */
    public function setManager($manager = null)
    {
        $this->manager = $manager;

        return $this;
    }
}
