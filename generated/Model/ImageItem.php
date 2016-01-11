<?php

namespace Docker\API\Model;

class ImageItem
{
    /**
     * @var string[]
     */
    protected $repoTags;
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string
     */
    protected $parentId;
    /**
     * @var int
     */
    protected $created;
    /**
     * @var int
     */
    protected $size;
    /**
     * @var int
     */
    protected $virtualSize;
    /**
     * @var string[]
     */
    protected $labels;
    /**
     * @var string[]
     */
    protected $repoDigests;

    /**
     * @return string[]
     */
    public function getRepoTags()
    {
        return $this->repoTags;
    }

    /**
     * @param string[] $repoTags
     *
     * @return self
     */
    public function setRepoTags(array $repoTags = null)
    {
        $this->repoTags = $repoTags;

        return $this;
    }

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
     * @return string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param string $parentId
     *
     * @return self
     */
    public function setParentId($parentId = null)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param int $created
     *
     * @return self
     */
    public function setCreated($created = null)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return self
     */
    public function setSize($size = null)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return int
     */
    public function getVirtualSize()
    {
        return $this->virtualSize;
    }

    /**
     * @param int $virtualSize
     *
     * @return self
     */
    public function setVirtualSize($virtualSize = null)
    {
        $this->virtualSize = $virtualSize;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param string[] $labels
     *
     * @return self
     */
    public function setLabels(\ArrayObject $labels = null)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRepoDigests()
    {
        return $this->repoDigests;
    }

    /**
     * @param string[] $repoDigests
     *
     * @return self
     */
    public function setRepoDigests(array $repoDigests = null)
    {
        $this->repoDigests = $repoDigests;

        return $this;
    }
}
