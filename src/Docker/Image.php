<?php

namespace Docker;

use Docker\Exception;

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
     * @param string $repository Name of the image
     * @param string $tag        Tag (version) of the image, default to latest
     */
    public function __construct($repository = null, $tag = 'latest')
    {
        $this->repository = $repository;
        $this->tag        = $tag;
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
}