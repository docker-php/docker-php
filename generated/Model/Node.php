<?php

namespace Docker\API\Model;

class Node
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
     * @var NodeSpec
     */
    protected $spec;
    /**
     * @var NodeDescription
     */
    protected $description;
    /**
     * @var NodeStatus
     */
    protected $status;
    /**
     * @var NodeManagerStatus
     */
    protected $managerStatus;

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
     * @return NodeSpec
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @param NodeSpec $spec
     *
     * @return self
     */
    public function setSpec(NodeSpec $spec = null)
    {
        $this->spec = $spec;

        return $this;
    }

    /**
     * @return NodeDescription
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param NodeDescription $description
     *
     * @return self
     */
    public function setDescription(NodeDescription $description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return NodeStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param NodeStatus $status
     *
     * @return self
     */
    public function setStatus(NodeStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return NodeManagerStatus
     */
    public function getManagerStatus()
    {
        return $this->managerStatus;
    }

    /**
     * @param NodeManagerStatus $managerStatus
     *
     * @return self
     */
    public function setManagerStatus(NodeManagerStatus $managerStatus = null)
    {
        $this->managerStatus = $managerStatus;

        return $this;
    }
}
