<?php

namespace Docker\Http\Exception;

use Docker\Exception as BaseException;
use Docker\Http\Request;

/**
 * Docker\Http\Exception\TimeoutException
 */
class TimeoutException extends BaseException
{
    public static function fromRequest(Request $request)
    {
        return new self(sprintf('Timed out while waiting for a response to "%s"', $request->getRequestUri()));
    }
}