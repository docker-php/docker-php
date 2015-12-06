<?php

namespace Docker\Manager;

use Docker\Event;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

/**
 * Docker\Manager\EventManager
 */
class EventManager
{

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @param \GuzzleHttp\Client
     */
    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get recent containers events
     *
     * @param $since
     * @param $until
     *
     * @return array
     */
    public function getEvents($since, $until)
    {
        try {
            $url = '/events?since=' . $since . '&until=' . $until;
            $response = $this->client->get([$url, []]);
            $contents = (string)$response->getBody();
            return $this->splitEvents($contents);
        } catch (RequestException $e) {
            throw $e;
        }
    }

    /**
     * Split json events text to array
     *
     * @param $contents
     *
     * @return array
     */
    public function splitEvents($contents)
    {
        $retVal = [];
        foreach (explode("\n", $contents) as $eventString) {
            if ($eventString) {
                $retVal[] = new Event(json_decode($eventString, true));
            }
        }
       return $retVal;
    }

}