<?php

namespace Docker\API\Model;

class VolumeList
{
    /**
     * @var Volume[]
     */
    protected $volumes;

    /**
     * @return Volume[]
     */
    public function getVolumes()
    {
        return $this->volumes;
    }

    /**
     * @param Volume[] $volumes
     *
     * @return self
     */
    public function setVolumes(array $volumes = null)
    {
        $this->volumes = $volumes;

        return $this;
    }
}
