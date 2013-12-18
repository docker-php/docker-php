<?php

namespace Docker\Http;

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Docker\Http\HeaderBag
 */
class HeaderBag implements IteratorAggregate, Countable
{
    /**
     * @var array
     */
    private $headers = array();

    /**
     * @param array $headers
     */
    public function __construct(array $headers = array())
    {
        $this->add($headers);
    }

    /**
     * @return integer
     */
    public function count()
    {
        return count($this->headers);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->headers);
    }

    /**
     * @return string
     */
    public function format()
    {
        $headers = [];

        foreach ($this->headers as $name => $value) {
            $name = implode('-', array_map('ucfirst', explode('-', $name)));
            $headers[] = sprintf('%s: %s', $name, $value);
        }

        sort($headers);

        return implode("\r\n", $headers);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * 
     * @return Docker\Http\HeaderBag
     */
    public function replace(array $headers)
    {
        $this->headers = array();
        $this->add($headers);

        return $this;
    }

    /**
     * @param array $headers
     * 
     * @return Docker\Http\HeaderBag
     */
    public function add(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * 
     * @return Docker\Http\HeaderBag
     */
    public function set($key, $value)
    {
        $this->headers[strtolower($key)] = $value;

        return $this;
    }

    /**
     * @param string $key
     * 
     * @return string
     */
    public function get($key)
    {
        $key = strtolower($key);

        return array_key_exists($key, $this->headers) ? $this->headers[$key] : null;
    }

    /**
     * @param string $key
     * 
     * @return boolean
     */
    public function has($key)
    {
        return array_key_exists(strtolower($key), $this->headers);
    }

    /**
     * @param string $key
     * 
     * @return Docker\Http\HeaderBag
     */
    public function remove($key)
    {
        $key = strtolower($key);

        if (array_key_exists($key, $this->headers)) {
            unset($this->headers[$key]);
        }

        return $this;
    }
}