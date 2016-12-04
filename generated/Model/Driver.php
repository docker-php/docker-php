<?php

namespace Docker\API\Model;

class Driver
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string[]|null
     */
    protected $options;

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
     * @return string[]|null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string[]|null $options
     *
     * @return self
     */
    public function setOptions($options = null)
    {
        $this->options = $options;

        return $this;
    }
}
