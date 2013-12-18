<?php

namespace Docker\Http;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class HeaderBag implements IteratorAggregate, Countable
{
    private $headers = array();

    public function __construct(array $headers = array())
    {
        $this->add($headers);
    }

    public function count()
    {
        return count($this->headers);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->headers);
    }

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

    public function all()
    {
        return $this->headers;
    }

    public function replace(array $headers)
    {
        $this->headers = array();
        $this->add($headers);
    }

    public function add(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->set($name, $value);
        }
    }

    public function set($key, $value)
    {
        $this->headers[strtolower($key)] = $value;

        return $this;
    }

    public function get($key)
    {
        $key = strtolower($key);

        return array_key_exists($key, $this->headers) ? $this->headers[$key] : null;
    }

    public function has($key)
    {
        return array_key_exists(strtolower($key), $this->headers);
    }

    public function remove($key)
    {
        $key = strtolower($key);

        if (array_key_exists($key, $this->headers)) {
            unset($this->headers[$key]);
        }

        return $this;
    }
}