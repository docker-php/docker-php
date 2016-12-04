<?php

namespace Docker\API\Model;

class Task
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
     * @var string
     */
    protected $name;
    /**
     * @var TaskSpec
     */
    protected $spec;
    /**
     * @var string
     */
    protected $serviceID;
    /**
     * @var int
     */
    protected $instance;
    /**
     * @var string
     */
    protected $nodeID;
    /**
     * @var Annotations
     */
    protected $serviceAnnotations;
    /**
     * @var TaskStatus
     */
    protected $status;
    /**
     * @var string
     */
    protected $desiredState;
    /**
     * @var NetworkAttachment[]|null
     */
    protected $networksAttachments;
    /**
     * @var Endpoint
     */
    protected $endpoint;

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
     * @return TaskSpec
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param TaskSpec $spec
     *
     * @return self
     */
    public function setSpec(TaskSpec $spec = null)
    {
        $this->spec = $spec;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceID()
    {
        return $this->serviceID;
    }

    /**
     * @param string $serviceID
     *
     * @return self
     */
    public function setServiceID($serviceID = null)
    {
        $this->serviceID = $serviceID;

        return $this;
    }

    /**
     * @return int
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param int $instance
     *
     * @return self
     */
    public function setInstance($instance = null)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * @return string
     */
    public function getNodeID()
    {
        return $this->nodeID;
    }

    /**
     * @param string $nodeID
     *
     * @return self
     */
    public function setNodeID($nodeID = null)
    {
        $this->nodeID = $nodeID;

        return $this;
    }

    /**
     * @return Annotations
     */
    public function getServiceAnnotations()
    {
        return $this->serviceAnnotations;
    }

    /**
     * @param Annotations $serviceAnnotations
     *
     * @return self
     */
    public function setServiceAnnotations(Annotations $serviceAnnotations = null)
    {
        $this->serviceAnnotations = $serviceAnnotations;

        return $this;
    }

    /**
     * @return TaskStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param TaskStatus $status
     *
     * @return self
     */
    public function setStatus(TaskStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return string
     */
    public function getDesiredState()
    {
        return $this->desiredState;
    }

    /**
     * @param string $desiredState
     *
     * @return self
     */
    public function setDesiredState($desiredState = null)
    {
        $this->desiredState = $desiredState;

        return $this;
    }

    /**
     * @return NetworkAttachment[]|null
     */
    public function getNetworksAttachments()
    {
        return $this->networksAttachments;
    }

    /**
     * @param NetworkAttachment[]|null $networksAttachments
     *
     * @return self
     */
    public function setNetworksAttachments($networksAttachments = null)
    {
        $this->networksAttachments = $networksAttachments;

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
}
