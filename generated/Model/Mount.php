<?php

namespace Docker\API\Model;

class Mount
{
    /**
     * @var string
     */
    protected $source;
    /**
     * @var string
     */
    protected $destination;
    /**
     * @var string
     */
    protected $mode;
    /**
     * @var bool
     */
    protected $rW;

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
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param string $destination
     *
     * @return self
     */
    public function setDestination($destination = null)
    {
        $this->destination = $destination;

        return $this;
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return self
     */
    public function setMode($mode = null)
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * @return bool
     */
    public function getRW()
    {
        return $this->rW;
    }

    /**
     * @param bool $rW
     *
     * @return self
     */
    public function setRW($rW = null)
    {
        $this->rW = $rW;

        return $this;
    }
}
