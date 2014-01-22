<?php

namespace Docker\Context;

/**
 * Docker\Context\ContextInterface
 */
interface ContextInterface
{
    /**
     * Get context as a stream
     *
     * @return ressource Return a stream ressource
     */
    public function toStream();
}
