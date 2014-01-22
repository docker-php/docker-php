<?php

namespace Docker\Context;

/**
 * Docker\Context\ContextInterface
 */
interface ContextInterface
{
    /**
     * @return boolean
     */
    public function isStreamed();

    /**
     * @return ressource|string
     */
    public function read();

    /**
     * Get context as a tar archive
     * 
     * @return string Tar content
     */
    public function toTar();

    /**
     * Get context as a stream
     *
     * @return ressource Return a stream ressource
     */
    public function toStream();
}
