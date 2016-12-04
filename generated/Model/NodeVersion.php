<?php

namespace Docker\API\Model;

class NodeVersion
{
    /**
     * @var string
     */
    protected $index;

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param string $index
     *
     * @return self
     */
    public function setIndex($index = null)
    {
        $this->index = $index;

        return $this;
    }
}
