<?php

namespace Docker;

/**
 * Docker\Event
 */
class Event
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $time;

    /**
     * @param array $raw
     */
    public function __construct($raw)
    {
        $this->status = $raw['status'];
        $this->id = $raw['id'];
        $this->from = $raw['from'];
        $this->time = $raw['time'];
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

}