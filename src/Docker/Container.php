<?php

namespace Docker;

use Docker\Exception\PortNotFoundException;
use LogicException;

/**
 * Docker\Container
 */
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
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $runtimeInformations = [];

    /**
     * @var null|integer
     */
    private $exitCode = null;

    /**
     * @var Image
     */
    private $image;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
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
     * @return Container
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
     * @param string  $protocol
     *
     * @return Port
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
            //       could use an instance of Port for example
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
     * @return Container
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
     * @return string
     */
    public function getName()
    {
        if (array_key_exists('Name', $this->runtimeInformations)) {
            return $this->runtimeInformations['Name'];
        }

        if (isset($this->name)) {
            return $this->name;
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return Container
     */
    public function setName($name)
    {
        if (!preg_match("/^\/?[a-zA-Z0-9][a-zA-Z0-9_.-]+$/", $name)) {
            throw new \Exception("Name was not correctly formatted.", 1);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param integer $exitCode
     *
     * @return Container
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
        if (null !== $this->exitCode) {
            return $this->exitCode;
        }

        if (is_array($this->runtimeInformations) && isset($this->runtimeInformations['State'])) {
            return $this->runtimeInformations['State']['ExitCode'];
        }

        throw new LogicException('Could not find an exit code');
    }

    /**
     * @param Port $ports
     *
     * @return Container
     */
    public function setExposedPorts($ports)
    {
        if ($ports instanceof PortSpecInterface) {
            $this->config['ExposedPorts'] = $ports->toExposedPorts();
        } else {
            $this->config['ExposedPorts'] = $ports;
        }

        return $this;
    }

    /**
     * @param integer $memory
     *
     * @return Container
     */
    public function setMemory($memory)
    {
        $this->config['Memory'] = (integer) $memory;

        return $this;
    }

    /**
     * @param string[] $env
     *
     * @return Container
     */
    public function addEnv(array $env)
    {
        $this->config['Env'] = array_merge($this->getEnv(), $env);

        return $this;
    }

    /**
     * @param array $env
     *
     * @return Container
     */
    public function setEnv(array $env)
    {
        $this->config['Env'] = $env;

        return $this;
    }

    /**
     * @return array
     */
    public function getEnv()
    {
        if (isset($this->runtimeInformations['Config']['Env'])) {
            return $this->runtimeInformations['Config']['Env'];
        }

        if (isset($this->config['Env'])) {
            return $this->config['Env'];
        }

        return [];
    }

    /**
     * @return array
     */
    public function getParsedEnv()
    {
        $env = [];

        foreach ($this->getEnv() as $raw) {
            list($key, $value) = explode('=', $raw);
            $env[$key] = $value;
        }

        return $env;
    }

    /**
     * @param string|Image $image
     *
     * @return Container
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
     * @return Image
     */
    public function getImage()
    {
        if (!$this->image instanceof Image) {
            $this->image = new Image();
            $this->image->setRepoTag($this->config['Image']);
        }

        return $this->image;
    }

    /**
     * @param array $cmd
     *
     * @return Container
     */
    public function setCmd(array $cmd)
    {
        $this->config['Cmd'] = $cmd;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return Container
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
