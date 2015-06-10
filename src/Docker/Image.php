<?php

namespace Docker;

/**
 * Docker\Image
 */
class Image
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $repository;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var int
     */
    private $created;

    /**
     * @param string $repository Name of the image
     * @param string $tag Tag (version) of the image, default "latest"
     * @param int $created unix timestamp
     */
    public function __construct($repository = null, $tag = 'latest', $created = null)
    {
        $this->repository = $repository;
        $this->tag        = $tag;
        $this->created    = $created;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (strlen($this->getRepository()) === 0) {
            return $this->getId();
        }

        if (strlen($this->getTag()) === 0) {
            return $this->getRepository();
        }

        return sprintf('%s:%s', $this->getRepository(), $this->getTag());
    }

    /**
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     *
     * @return Image
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @param string $id
     *
     * @return Image
     */
    public function setId($id)
    {
        $this->id = $id;

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
     * @param string $tag
     *
     * @return Image
     */
    public function setTag($tag = 'latest')
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
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
     * @return $this
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }



}
