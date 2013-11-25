<?php

namespace Docker;

interface PortSpecInterface
{
    public function toSpec();

    public function toExposedPorts();
}