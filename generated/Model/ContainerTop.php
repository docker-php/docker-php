<?php

namespace Docker\API\Model;

class ContainerTop
{
    /**
     * @var string[]
     */
    protected $titles;
    /**
     * @var string[][]
     */
    protected $processes;

    /**
     * @return string[]
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @param string[] $titles
     *
     * @return self
     */
    public function setTitles(array $titles = null)
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * @return string[][]
     */
    public function getProcesses()
    {
        return $this->processes;
    }

    /**
     * @param string[][] $processes
     *
     * @return self
     */
    public function setProcesses(array $processes = null)
    {
        $this->processes = $processes;

        return $this;
    }
}
