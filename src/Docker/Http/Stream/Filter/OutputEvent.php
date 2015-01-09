<?php

namespace Docker\Http\Stream\Filter;

use GuzzleHttp\Event\EventInterface;

class OutputEvent implements EventInterface
{
    const TYPE_STDOUT = 0;
    const TYPE_STDIN  = 1;
    const TYPE_STDERR = 2;

    private $stopped = false;

    private $content;

    private $type;

    public function __construct($content, $type = null)
    {
        $this->content = $content;
        $this->type    = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped()
    {
        return $this->stopped;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation()
    {
        $this->stopped = true;
    }

    /**
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }
}
