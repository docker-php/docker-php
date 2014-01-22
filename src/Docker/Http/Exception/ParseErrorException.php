<?php

namespace Docker\Http\Exception;

use Docker\Exception as BaseException;
use Docker\Http\Request;

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
     * @var Docker\Http\Request
     */
    private $request;

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

    /**
     * @param Docker\Http\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return Docker\Http\Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}