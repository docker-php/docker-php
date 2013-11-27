<?php

namespace Docker;

use Docker\Exception;

class Port implements PortSpecInterface
{
    private $port;

    private $protocol = 'tcp';

    private $hostIp;

    private $hostPort;

    public function __construct($raw)
    {
        $parsed = static::parse($raw);
        $filtered = array_filter($parsed);

        foreach ($filtered as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return integer
     */
    public function getPort()
    {
        return (integer) $this->port;
    }

    /**
     * @return string|null
     */
    public function getHostIp()
    {
        return $this->hostIp;
    }

    /**
     * @return integer|null
     */
    public function getHostPort()
    {
        return $this->hostPort;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return array
     */
    public function toSpec()
    {
        return [
            $this->port.'/'.$this->protocol => [
                [
                    'HostIp' => $this->hostIp,
                    'HostPort' => (string) $this->hostPort
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function toExposedPorts()
    {
        return [$this->port.'/'.$this->protocol => []];
    }

    /**
     * @param string $raw
     * 
     * @return array
     * 
     * [[hostIp:][hostPort]:]port[/protocol]
     */
    static public function parse($raw)
    {
        if (!preg_match('/(?:(?<hostIp>[0-9\.]{7,15}):)?(?:(?<hostPort>\d{1,5}|):)?(?<port>\d{1,5})(?:\/(?<protocol>\w+))?/', $raw, $matches)) {
            throw new Exception('Invalid port specification "'.$raw.'"');
        }

        $parsed = [];

        foreach (['hostIp', 'hostPort', 'port', 'protocol'] as $key) {
            if (array_key_exists($key, $matches)) {
                $parsed[$key] = strlen($matches[$key]) > 0 ? $matches[$key] : null;
            } else {
                $parsed[$key] = null;
            }
        }

        return $parsed;
    }
}