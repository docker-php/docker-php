<?php

namespace Docker\API\Model;

class NodePlatform
{
    /**
     * @var string
     */
    protected $architecture;
    /**
     * @var string
     */
    protected $oS;

    /**
     * @return string
     */
    public function getArchitecture()
    {
        return $this->architecture;
    }

    /**
     * @param string $architecture
     *
     * @return self
     */
    public function setArchitecture($architecture = null)
    {
        $this->architecture = $architecture;

        return $this;
    }

    /**
     * @return string
     */
    public function getOS()
    {
        return $this->oS;
    }

    /**
     * @param string $oS
     *
     * @return self
     */
    public function setOS($oS = null)
    {
        $this->oS = $oS;

        return $this;
    }
}
