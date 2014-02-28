<?php

namespace Docker\Http\Exception;

use Docker\Http\Exception as HttpException;
use Docker\Http\Request;

/**
 * Docker\Http\Exception\ParseErrorException
 */
class ParseErrorException extends HttpException
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

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}