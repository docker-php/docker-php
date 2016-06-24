<?php

namespace Docker\API\Model;

class ContainerConfig
{
    /**
     * @var string
     */
    protected $id;
    /**
     * @var string[]|null
     */
    protected $names;
    /**
     * @var string
     */
    protected $image;
    /**
     * @var string
     */
    protected $imageID;
    /**
     * @var string
     */
    protected $command;
    /**
     * @var int
     */
    protected $created;
    /**
     * @var string
     */
    protected $state;
    /**
     * @var string
     */
    protected $status;
    /**
     * @var Port[]|null
     */
    protected $ports;
    /**
     * @var string[]
     */
    protected $labels;
    /**
     * @var int
     */
    protected $sizeRw;
    /**
     * @var int
     */
    protected $sizeRootFs;
    /**
     * @var int
     */
    protected $hostname;
    /**
     * @var int
     */
    protected $domainname;
    /**
     * @var int
     */
    protected $user;
    /**
     * @var bool
     */
    protected $attachStdin;
    /**
     * @var bool
     */
    protected $attachStdout;
    /**
     * @var bool
     */
    protected $attachStderr;
    /**
     * @var bool
     */
    protected $tty;
    /**
     * @var bool
     */
    protected $openStdin;
    /**
     * @var bool
     */
    protected $stdinOnce;
    /**
     * @var string[]|null
     */
    protected $env;
    /**
     * @var string[]|string
     */
    protected $cmd;
    /**
     * @var string[]|string
     */
    protected $entrypoint;
    /**
     * @var mixed[]|null
     */
    protected $volumes;
    /**
     * @var string
     */
    protected $workingDir;
    /**
     * @var bool
     */
    protected $networkDisabled;
    /**
     * @var string
     */
    protected $macAddress;
    /**
     * @var mixed[]
     */
    protected $exposedPorts;
    /**
     * @var NetworkConfig
     */
    protected $networkSettings;
    /**
     * @var HostConfig
     */
    protected $hostConfig;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return self
     */
    public function setId($id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @param string[]|null $names
     *
     * @return self
     */
    public function setNames($names = null)
    {
        $this->names = $names;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return self
     */
    public function setImage($image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageID()
    {
        return $this->imageID;
    }

    /**
     * @param string $imageID
     *
     * @return self
     */
    public function setImageID($imageID = null)
    {
        $this->imageID = $imageID;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param string $command
     *
     * @return self
     */
    public function setCommand($command = null)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param int $created
     *
     * @return self
     */
    public function setCreated($created = null)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return self
     */
    public function setState($state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Port[]|null
     */
    public function getPorts()
    {
        return $this->ports;
    }

    /**
     * @param Port[]|null $ports
     *
     * @return self
     */
    public function setPorts($ports = null)
    {
        $this->ports = $ports;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param string[] $labels
     *
     * @return self
     */
    public function setLabels(\ArrayObject $labels = null)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return int
     */
    public function getSizeRw()
    {
        return $this->sizeRw;
    }

    /**
     * @param int $sizeRw
     *
     * @return self
     */
    public function setSizeRw($sizeRw = null)
    {
        $this->sizeRw = $sizeRw;

        return $this;
    }

    /**
     * @return int
     */
    public function getSizeRootFs()
    {
        return $this->sizeRootFs;
    }

    /**
     * @param int $sizeRootFs
     *
     * @return self
     */
    public function setSizeRootFs($sizeRootFs = null)
    {
        $this->sizeRootFs = $sizeRootFs;

        return $this;
    }

    /**
     * @return int
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @param int $hostname
     *
     * @return self
     */
    public function setHostname($hostname = null)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * @return int
     */
    public function getDomainname()
    {
        return $this->domainname;
    }

    /**
     * @param int $domainname
     *
     * @return self
     */
    public function setDomainname($domainname = null)
    {
        $this->domainname = $domainname;

        return $this;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     *
     * @return self
     */
    public function setUser($user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAttachStdin()
    {
        return $this->attachStdin;
    }

    /**
     * @param bool $attachStdin
     *
     * @return self
     */
    public function setAttachStdin($attachStdin = null)
    {
        $this->attachStdin = $attachStdin;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAttachStdout()
    {
        return $this->attachStdout;
    }

    /**
     * @param bool $attachStdout
     *
     * @return self
     */
    public function setAttachStdout($attachStdout = null)
    {
        $this->attachStdout = $attachStdout;

        return $this;
    }

    /**
     * @return bool
     */
    public function getAttachStderr()
    {
        return $this->attachStderr;
    }

    /**
     * @param bool $attachStderr
     *
     * @return self
     */
    public function setAttachStderr($attachStderr = null)
    {
        $this->attachStderr = $attachStderr;

        return $this;
    }

    /**
     * @return bool
     */
    public function getTty()
    {
        return $this->tty;
    }

    /**
     * @param bool $tty
     *
     * @return self
     */
    public function setTty($tty = null)
    {
        $this->tty = $tty;

        return $this;
    }

    /**
     * @return bool
     */
    public function getOpenStdin()
    {
        return $this->openStdin;
    }

    /**
     * @param bool $openStdin
     *
     * @return self
     */
    public function setOpenStdin($openStdin = null)
    {
        $this->openStdin = $openStdin;

        return $this;
    }

    /**
     * @return bool
     */
    public function getStdinOnce()
    {
        return $this->stdinOnce;
    }

    /**
     * @param bool $stdinOnce
     *
     * @return self
     */
    public function setStdinOnce($stdinOnce = null)
    {
        $this->stdinOnce = $stdinOnce;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param string[]|null $env
     *
     * @return self
     */
    public function setEnv($env = null)
    {
        $this->env = $env;

        return $this;
    }

    /**
     * @return string[]|string
     */
    public function getCmd()
    {
        return $this->cmd;
    }

    /**
     * @param string[]|string $cmd
     *
     * @return self
     */
    public function setCmd($cmd = null)
    {
        $this->cmd = $cmd;

        return $this;
    }

    /**
     * @return string[]|string
     */
    public function getEntrypoint()
    {
        return $this->entrypoint;
    }

    /**
     * @param string[]|string $entrypoint
     *
     * @return self
     */
    public function setEntrypoint($entrypoint = null)
    {
        $this->entrypoint = $entrypoint;

        return $this;
    }

    /**
     * @return mixed[]|null
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    /**
     * @param mixed[]|null $volumes
     *
     * @return self
     */
    public function setVolumes($volumes = null)
    {
        $this->volumes = $volumes;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkingDir()
    {
        return $this->workingDir;
    }

    /**
     * @param string $workingDir
     *
     * @return self
     */
    public function setWorkingDir($workingDir = null)
    {
        $this->workingDir = $workingDir;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNetworkDisabled()
    {
        return $this->networkDisabled;
    }

    /**
     * @param bool $networkDisabled
     *
     * @return self
     */
    public function setNetworkDisabled($networkDisabled = null)
    {
        $this->networkDisabled = $networkDisabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getMacAddress()
    {
        return $this->macAddress;
    }

    /**
     * @param string $macAddress
     *
     * @return self
     */
    public function setMacAddress($macAddress = null)
    {
        $this->macAddress = $macAddress;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function getExposedPorts()
    {
        return $this->exposedPorts;
    }

    /**
     * @param mixed[] $exposedPorts
     *
     * @return self
     */
    public function setExposedPorts(\ArrayObject $exposedPorts = null)
    {
        $this->exposedPorts = $exposedPorts;

        return $this;
    }

    /**
     * @return NetworkConfig
     */
    public function getNetworkSettings()
    {
        return $this->networkSettings;
    }

    /**
     * @param NetworkConfig $networkSettings
     *
     * @return self
     */
    public function setNetworkSettings(NetworkConfig $networkSettings = null)
    {
        $this->networkSettings = $networkSettings;

        return $this;
    }

    /**
     * @return HostConfig
     */
    public function getHostConfig()
    {
        return $this->hostConfig;
    }

    /**
     * @param HostConfig $hostConfig
     *
     * @return self
     */
    public function setHostConfig(HostConfig $hostConfig = null)
    {
        $this->hostConfig = $hostConfig;

        return $this;
    }
}
