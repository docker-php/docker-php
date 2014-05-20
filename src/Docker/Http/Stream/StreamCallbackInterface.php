<?php
/**
 * Created by PhpStorm.
 * User: brouznouf
 * Date: 01/05/2014
 * Time: 21:02
 */

namespace Docker\Http\Stream;


interface StreamCallbackInterface
{
    const STDIN  = 0;
    const STDOUT = 1;
    const STDERR = 2;

    /**
     * Read a stream by block
     *
     * @return string
     */
    public function readWithCallback(callable $callback);
} 