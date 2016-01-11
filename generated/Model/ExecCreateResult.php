<?php

namespace Docker\API\Model;

class ExecCreateResult
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string[]
     */
    protected $warnings;

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
     * @return string[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @param string[] $warnings
     *
     * @return self
     */
    public function setWarnings(array $warnings = null)
    {
        $this->warnings = $warnings;

        return $this;
    }
}
