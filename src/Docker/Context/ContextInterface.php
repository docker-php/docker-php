<?php

namespace Docker\Context;

interface ContextInterface
{
    /**
     * Get context as a stream
     *
     * return ressource Return a stream ressource
     */
    public function toStream();
}