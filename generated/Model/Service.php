<?php

namespace Docker\API\Model;

class Service
{
    /**
     * @var string
     */
    protected $iD;
    /**
     * @var NodeVersion
     */
    protected $version;
    /**
     * @var \DateTime
     */
    protected $createdAt;
    /**
     * @var \DateTime
     */
    protected $updatedAt;
    /**
     * @var ServiceSpec
     */
    protected $spec;
    /**
     * @var Endpoint
     */
    protected $endpoint;
    /**
     * @var UpdateStatus
     */
    protected $updateStatus;

    /**
     * @return string
     */
    public function getID()
    {
        return $this->iD;
    }

    /**
     * @param string $iD
     *
     * @return self
     */
    public function setID($iD = null)
    {
        $this->iD = $iD;

        return $this;
    }

    /**
     * @return NodeVersion
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param NodeVersion $version
     *
     * @return self
     */
    public function setVersion(NodeVersion $version = null)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return ServiceSpec
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param ServiceSpec $spec
     *
     * @return self
     */
    public function setSpec(ServiceSpec $spec = null)
    {
        $this->spec = $spec;

        return $this;
    }

    /**
     * @return Endpoint
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param Endpoint $endpoint
     *
     * @return self
     */
    public function setEndpoint(Endpoint $endpoint = null)
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * @return UpdateStatus
     */
    public function getUpdateStatus()
    {
        return $this->updateStatus;
    }

    /**
     * @param UpdateStatus $updateStatus
     *
     * @return self
     */
    public function setUpdateStatus(UpdateStatus $updateStatus = null)
    {
        $this->updateStatus = $updateStatus;

        return $this;
    }
}
