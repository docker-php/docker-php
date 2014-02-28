<?php

namespace Docker\Http;

use Docker\Exception as BaseException;

class Exception extends BaseException
{
    /**
     * @var Docker\Http\Request
     */
    private $request;

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