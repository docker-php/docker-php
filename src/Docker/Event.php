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

    /**
     * Split json events text to array
     *
     * @param $contents
     * @return array
     */
    public static function splitEvents($contents)
    {
        $retVal = [];
        $numOfBrackets = 0;
        $currentString = '';
        for ($i = 0; $i < strlen($contents); $i++) {
            if ($contents{$i} == '{') {
                $numOfBrackets++;
            }
            if ($contents{$i} == '}') {
                $numOfBrackets--;
            }
            $currentString .= $contents{$i};
            if ($numOfBrackets == 0 ) {
                if (!empty($currentString)) {
                    $retVal[] = new Event(json_decode($currentString, true));
                }
                $currentString = '';
            }
        }

        return $retVal;
    }
}
