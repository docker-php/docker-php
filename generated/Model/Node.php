<?php

namespace Docker\API\Model;

class Node
{
    /**
     * @var string
     */
    protected $addr;
    /**
     * @var int
     */
    protected $cpus;
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $ip;
    /**
     * @var string[]
     */
    protected $labels;
    /**
     * @var int
     */
    protected $memory;
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $version;

    /**
     * @return string
     */
    public function getAddr()
    {
        return $this->addr;
    }

    /**
     * @param string $addr
     *
     * @return self
     */
    public function setAddr($addr = null)
    {
        $this->addr = $addr;

        return $this;
    }

    /**
     * @return int
     */
    public function getCpus()
    {
        return $this->cpus;
    }

    /**
     * @param int $cpus
     *
     * @return self
     */
    public function setCpus($cpus = null)
    {
        $this->cpus = $cpus;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return self
     */
    public function setId($id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getIP()
    {
        return $this->ip;
    }

    /**
     * @param string $id
     *
     * @return self
     */
    public function setIP($ip = null)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param string[] $labels
     *
     * @return self
     */
    public function setLabels(\ArrayObject $labels = null)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * @param int $memory
     *
     * @return self
     */
    public function setMemory($memory = null)
    {
        $this->memory = $memory;

        return $this;
    }

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
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return self
     */
    public function setVersion($version = null)
    {
        $this->version = $version;

        return $this;
    }
}
