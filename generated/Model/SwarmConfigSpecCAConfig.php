<?php

namespace Docker\API\Model;

class SwarmConfigSpecCAConfig
{
    /**
     * @var bool
     */
    protected $nodeCertExpiry;
    /**
     * @var SwarmConfigSpecCAConfigExternalCA
     */
    protected $externalCA;

    /**
     * @return bool
     */
    public function getNodeCertExpiry()
    {
        return $this->nodeCertExpiry;
    }

    /**
     * @param bool $nodeCertExpiry
     *
     * @return self
     */
    public function setNodeCertExpiry($nodeCertExpiry = null)
    {
        $this->nodeCertExpiry = $nodeCertExpiry;

        return $this;
    }

    /**
     * @return SwarmConfigSpecCAConfigExternalCA
     */
    public function getExternalCA()
    {
        return $this->externalCA;
    }

    /**
     * @param SwarmConfigSpecCAConfigExternalCA $externalCA
     *
     * @return self
     */
    public function setExternalCA(SwarmConfigSpecCAConfigExternalCA $externalCA = null)
    {
        $this->externalCA = $externalCA;

        return $this;
    }
}
