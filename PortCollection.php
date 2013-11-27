<?php

namespace Docker;

use Docker\Exception;

class PortCollection implements PortSpecInterface
{
    private $ports;

    public function __construct()
    {
        $args = func_get_args();

        foreach ($args as $port) {
            if ($port instanceof Port) {
                $this->add($port);
            } elseif (is_string($port) || is_integer($port)) {
                $this->add(new Port($port));
            } else {
                throw new Exception('Invalid port definition "('.gettype($port).') '.var_export($port, true).'"');
            }
        }
    }

    public function toSpec()
    {
        $spec = [];

        foreach ($this->ports as $port) {
            $spec = array_merge($spec, $port->toSpec());
        }

        return $spec;
    }

    public function toExposedPorts()
    {
        $exposed = [];

        foreach ($this->ports as $port) {
            $exposed = array_merge($exposed, $port->toExposedPorts());
        }

        return $exposed;
    }

    public function add(Port $port)
    {
        $this->ports[] = $port;
    }

    public function all()
    {
        return $this->ports;
    }
}