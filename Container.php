<?php

namespace Docker;

use Docker\Exception\PortNotFoundException;

use Guzzle\Http\Client;

class Container
{
    const STATUS_NEW = 0;

    const STATUS_CREATED = 1;

    const STATUS_STARTED = 2;

    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $config = array();

    /**
     * @var array
     */
    private $runtimeInformations = array();

    /**
     * @var null|integer
     */
    private $exitCode = null;

    /**
     * @var Docker\Image
     */
    private $image;

    /**
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        $this->config = $config;
    }

    /**
     * @return boolean
     */
    public function exists()
    {
        return strlen($this->id) > 0;
    }

    /**
     * @return array
     */
    public function getRuntimeInformations()
    {
        return $this->runtimeInformations;
    }

    /**
     * @param array $runtimeInformations
     * 
     * @return Docker\Container
     */
    public function setRuntimeInformations($runtimeInformations)
    {
        $this->runtimeInformations = $runtimeInformations;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param integer $port
     * @param string $protocol
     * 
     * @return Docker\Port
     */
    public function getMappedPort($port, $protocol = 'tcp')
    {
        // Problem with $protohuik as a variable name? Harass @futurecat.
        $protohuik = $port.'/'.$protocol;

        if (!array_key_exists($protohuik, $this->runtimeInformations['NetworkSettings']['Ports'])) {
            throw new PortNotFoundException($port, $protocol);
        }

        $portInfo = $this->runtimeInformations['NetworkSettings']['Ports'][$protohuik];

        return new Port(sprintf('%s:%s:%s/%s', $portInfo[0]['HostIp'], $portInfo[0]['HostPort'], $port, $protocol));
    }

    /**
     * Accepts both (eg) 80 or 80/tcp as inputs.
     * 
     * @param integer|string ...$ports
     * 
     * @return array
     */
    public function getMappedPorts()
    {
        $ports = func_get_args();
        $mappedPorts = [];

        foreach ($ports as $protohuik) {
            // @todo better validation of $protohuik
            $parts = explode('/', $protohuik);

            if (count($parts) === 1) {
                $parts[] = 'tcp';
            }

            list($port, $protocol) = $parts;

            try {
                $mappedPort = $this->getMappedPort($port, $protocol);
            } catch (PortNotFoundException $e) {
                continue;
            }

            $mappedPorts[] = $mappedPort;
        }

        return $mappedPorts;
    }

    /**
     * @param string $id
     * 
     * @return Docker\Container
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $exitCode
     * 
     * @return Docker\Container
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = (integer) $exitCode;

        return $this;
    }

    /**
     * @return integer
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * @param array|PortSpecInterface $ports
     */
    public function setExposedPorts($ports)
    {
        if ($ports instanceof PortSpecInterface) {
            $this->config['ExposedPorts'] = $ports->toExposedPorts();            
        } else {
            $this->config['ExposedPorts'] = $ports;
        }
    }

    /**
     * @param integer $memory
     * 
     * @return Docker\Container;
     */
    public function setMemory($memory)
    {
        $this->config['Memory'] = (integer) $memory;

        return $this;
    }

    /**
     * @param array $env
     * 
     * @return Docker\Container
     */
    public function setEnv(array $env)
    {
        $this->config['Env'] = $env;

        return $this;
    }

    /**
     * @param string|Docker\Image $image
     * 
     * @return Docker\Container
     */
    public function setImage($image)
    {
        if ($image instanceof Image) {
            $this->image = $image;
        }

        $this->config['Image'] = (string) $image;

        return $this;
    }

    /**
     * @param array $cmd
     * 
     * @return Docker\Container
     */
    public function setCmd(array $cmd)
    {
        $this->config['Cmd'] = $cmd;
    }
}