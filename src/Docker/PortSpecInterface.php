<?php

namespace Docker;

/**
 * Docker\PortSpecInterface
 */
interface PortSpecInterface
{
    /**
     * @return array
     */
    public function toSpec();

    /**
     * @return array
     */
    public function toExposedPorts();
}