<?php

namespace Docker\Http\Adapter;

use Zend\Http\Client\Adapter\Socket;
use Zend\Stdlib\ErrorHandler;
use Zend\Http\Client\Adapter\Exception\RuntimeException;

class UnixSocket extends Socket
{
    private $entryPoint = null;

    public function setEntryPoint($entryPoint)
    {
        //If we are already connected close connection
        if (is_resource($this->socket)) {
            $this->close();
        }

        $this->entryPoint = $entryPoint;
    }

    /*
     * @see \Zend\Http\Client\Adapter\AdapterInterface::connect()
     */
    public function connect($host, $port = 80, $secure = false)
    {
        if ($this->entryPoint === null) {
            return parent::connect($host, $port, $secure);
        }

        $this->config['timeout'] = isset($this->config['timeout']) ? $this->config['timeout'] : 10;

        if (!is_resource($this->socket) || !$this->config['keepalive']) {
            $flags = STREAM_CLIENT_CONNECT;
            if ($this->config['persistent']) {
                $flags |= STREAM_CLIENT_PERSISTENT;
            }

            ErrorHandler::start();
            $this->socket = stream_socket_client(
                $this->entryPoint,
                $errno,
                $errstr,
                (int) $this->config['timeout'],
                $flags
            );
            $error = ErrorHandler::stop();

            if (!$this->socket) {
                $this->close();
                throw new RuntimeException(
                    sprintf(
                        'Unable to connect to %s:%s',
                        $this->entryPoint,
                        ($error ? ' . Error #' . $error->getCode() . ': ' . $error->getMessage() : '')
                    ),
                    0,
                    $error
                );
            }

            // Set the stream timeout
            if (!stream_set_timeout($this->socket, (int) $this->config['timeout'])) {
                throw new RuntimeException('Unable to set the connection timeout');
            }

            //Fake connection
            $this->connectedTo = array('tcp://localhost', 80);
        }
    }

    /**
     * Send request to the remote server
     *
     * @param string        $method
     * @param \Zend\Uri\Uri $uri
     * @param string        $httpVer
     * @param array         $headers
     * @param string        $body
     * @throws AdapterException\RuntimeException
     * @return string Request as string
     */
    public function write($method, $uri, $httpVer = '1.1', $headers = array(), $body = '')
    {
        if ($this->entryPoint === null) {
            return parent::write($method, $uri, $httpVer, $headers, $body);
        }

        $uri->setScheme('http');
        $uri->setHost('localhost');
        $uri->setPort(80);

        return parent::write($method, $uri, $httpVer, $headers, $body);
    }
}