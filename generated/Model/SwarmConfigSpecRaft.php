<?php

namespace Docker\API\Model;

class SwarmConfigSpecRaft
{
    /**
     * @var int
     */
    protected $snapshotInterval;
    /**
     * @var int
     */
    protected $keepOldSnapshots;
    /**
     * @var int
     */
    protected $logEntriesForSlowFollowers;
    /**
     * @var int
     */
    protected $heartbeatTick;
    /**
     * @var int
     */
    protected $electionTick;

    /**
     * @return int
     */
    public function getSnapshotInterval()
    {
        return $this->snapshotInterval;
    }

    /**
     * @param int $snapshotInterval
     *
     * @return self
     */
    public function setSnapshotInterval($snapshotInterval = null)
    {
        $this->snapshotInterval = $snapshotInterval;

        return $this;
    }

    /**
     * @return int
     */
    public function getKeepOldSnapshots()
    {
        return $this->keepOldSnapshots;
    }

    /**
     * @param int $keepOldSnapshots
     *
     * @return self
     */
    public function setKeepOldSnapshots($keepOldSnapshots = null)
    {
        $this->keepOldSnapshots = $keepOldSnapshots;

        return $this;
    }

    /**
     * @return int
     */
    public function getLogEntriesForSlowFollowers()
    {
        return $this->logEntriesForSlowFollowers;
    }

    /**
     * @param int $logEntriesForSlowFollowers
     *
     * @return self
     */
    public function setLogEntriesForSlowFollowers($logEntriesForSlowFollowers = null)
    {
        $this->logEntriesForSlowFollowers = $logEntriesForSlowFollowers;

        return $this;
    }

    /**
     * @return int
     */
    public function getHeartbeatTick()
    {
        return $this->heartbeatTick;
    }

    /**
     * @param int $heartbeatTick
     *
     * @return self
     */
    public function setHeartbeatTick($heartbeatTick = null)
    {
        $this->heartbeatTick = $heartbeatTick;

        return $this;
    }

    /**
     * @return int
     */
    public function getElectionTick()
    {
        return $this->electionTick;
    }

    /**
     * @param int $electionTick
     *
     * @return self
     */
    public function setElectionTick($electionTick = null)
    {
        $this->electionTick = $electionTick;

        return $this;
    }
}
