<?php

namespace Docker\API\Model;

class NetworkAttachmentConfig
{
    /**
     * @var string
     */
    protected $target;
    /**
     * @var string[]|null
     */
    protected $aliases;

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     *
     * @return self
     */
    public function setTarget($target = null)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getAliases()
    {
        return $this->aliases;
    }

    /**
     * @param string[]|null $aliases
     *
     * @return self
     */
    public function setAliases($aliases = null)
    {
        $this->aliases = $aliases;

        return $this;
    }
}
