<?php

namespace Docker;

use Docker\Exception;

class Port implements PortSpecInterface
{
    private $raw;

    private $port;

    private $protocol = 'tcp';

    private $hostIp;

    private $hostPort;

    public function __construct($raw)
    {
        $this->raw = $raw;

        $parsed = static::parse($raw, ['protocol' => $this->protocol]);

        $this->port = (integer) $parsed['port'];
        $this->protocol = $parsed['protocol'];
        $this->hostIp = (string) $parsed['hostIp'];
        $this->hostPort = (strlen($parsed['hostPort']) > 0) ? (integer) $parsed['hostPort'] : '';
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
     * [[hostIp:]hostPort:]port[/protocol]
     */
    static public function parse($raw, $defaults = array())
    {
        $parsed = array_replace([
            'protocol' => null,
            'port' => null,
            'hostIp' => null,
            'hostPort' => null
        ], $defaults);

        $parts = explode('/', $raw);

        if (count($parts) > 1) {
            $parsed['protocol'] = $parts[1];
        }

        $parts = explode(':', $parts[0]);

        /**
         * This is very naive and will fail on things like HostIp::port/protocol
         * 
         * @todo make this more clever, maybe using a token parser, or adding data validation?
         */
        switch (count($parts)) {
            case 1:
                $parsed['port'] = (integer) $parts[0];
                break;
            case 2:
                $parsed['hostPort'] = (integer) $parts[0];
                $parsed['port'] = (integer) $parts[1];
                break;
            case 3:
                $parsed['hostIp'] = $parts[0];
                $parsed['hostPort'] = (integer) $parts[1];
                $parsed['port'] = (integer) $parts[2];
                break;
            default:
                throw new Exception('Invalid port specification "'.$raw.'"');
        }

        return $parsed;
    }
}