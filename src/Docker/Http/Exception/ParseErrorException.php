<?php

namespace Docker\Http\Exception;

use Docker\Exception as BaseException;

/**
 * Docker\Http\Exception\ParseErrorException
 */
class ParseErrorException extends BaseException
{
    /**
     * @var string
     */
    private $content;

    /**
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = $content;

        parent::__construct();
    }

    public function getContent()
    {
        return $this->content;
    }
}