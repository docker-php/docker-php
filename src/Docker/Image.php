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
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getName();
        } catch (Exception $e) {
            return 'ERROR: '.$e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (strlen($this->getRepository()) === 0) {
            throw new Exception('This image does not have a repository');
        }

        if (strlen($this->getTag()) > 0) {
            return $this->getRepository().':'.$this->getTag();
        }

        return $this->getRepository();
    }

    /**
     * @param string $id
     * 
     * @return Docker\Image
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
     * @param string $repository
     * 
     * @return Docker\Image
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param string $tag
     * 
     * @return Docker\Image
     */
    public function setTag($tag)
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