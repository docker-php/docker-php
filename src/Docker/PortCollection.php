<?php

namespace Docker;

/**
 * Docker\PortCollection
 */
class PortCollection implements PortSpecInterface
{
    /**
     * @var Port[]
     */
    private $ports;

    /**
     * @param integer|string ...$ports
     *
     * @throws \Exception When a port definition is invalid
     */
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

    /**
     * @return array
     */
    public function toSpec()
    {
        $spec = [];

        foreach ($this->ports as $port) {
            $spec = array_merge($spec, $port->toSpec());
        }

        return $spec;
    }

    /**
     * @return array
     */
    public function toExposedPorts()
    {
        $exposed = [];

        foreach ($this->ports as $port) {
            $exposed = array_merge($exposed, $port->toExposedPorts());
        }

        return $exposed;
    }

    /**
     * Add a port
     *
     * @param \Docker\Port $port Port to add
     *
     * @return PortCollection
     */
    public function add(Port $port)
    {
        $this->ports[] = $port;

        return $this;
    }

    /**
     * @return Port[]
     */
    public function all()
    {
        return $this->ports;
    }
}
