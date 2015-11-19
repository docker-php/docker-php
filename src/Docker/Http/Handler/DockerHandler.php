<?php

namespace Docker\Http\Handler;

use Docker\Http\Stream\Filter\OutputEvent;
use GuzzleHttp\Event\EmitterInterface;
use GuzzleHttp\Ring\Core;
use GuzzleHttp\Ring\Exception\ConnectException;
use GuzzleHttp\Ring\Exception\RingException;
use GuzzleHttp\Ring\Future\CompletedFutureArray;
use GuzzleHttp\Stream\Stream;

class DockerHandler
{
    const CHUNK_SIZE = 8192;

    /** @var string */
    private $entrypoint;

    /** @var resource A stream context resource */
    private $context;

    /** @var boolean Whether stream is encrypted with TLS */
    private $useTls;

    /** @var EmitterInterface */
    protected $emitter;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (isset($options['entrypoint'])) {
            $this->entrypoint = $options['entrypoint'];
        }
        if (isset($options['useTls'])) {
            $this->useTls = $options['useTls'];
        }
        if (isset($options['emitter'])) {
            $this->emitter = $options['emitter'];
        }
        $this->context = isset($options['context'])
            ? $options['context'] : stream_context_create();

        stream_filter_register('chunk', '\Docker\Http\Stream\Filter\Chunk');
        stream_filter_register('event', '\Docker\Http\Stream\Filter\Event');
    }

    /**
     * @param array $request
     * @return CompletedFutureArray
     */
    public function __invoke(array $request)
    {
        $url = Core::url($request);
        Core::doSleep($request);

        try {
            // Does not support the expect header.
            $request = Core::removeHeader($request, 'Expect');
            $stream = $this->createStream($url, $request);

            return $this->createResponse($request, $stream);
        } catch (RingException $e) {
            return $this->createErrorResponse($url, $e);
        }
    }

    /**
     * @param array $request
     * @param $value
     */
    public function addCallback(array &$request, $value)
    {
        $this->emitter->on('response.output', function (OutputEvent $event) use ($value, $request) {
            $value($event->getContent(), $event->getType());
        });
    }

    /**
     * @param array $request
     * @param $value
     */
    private function addTimeout(array &$request, $value)
    {
        $request['client']['timeout'] = $value;
    }

    /**
     * @param string $url
     * @param array $request
     * @return resource
     */
    private function createStream($url, array &$request)
    {
        static $methods;
        if (!$methods) {
            $methods = array_flip(get_class_methods(__CLASS__));
        }

        // HTTP/1.1 streams using the PHP stream wrapper require a
        // Connection: close header
        if ((!isset($request['version']) || $request['version'] == '1.1')
            && !Core::hasHeader($request, 'Connection')
        ) {
            $request['headers']['Connection'] = ['close'];
        }

        if (isset($request['client']['stream']) && $request['client']['stream']) {
            $request = Core::setHeader($request, 'Transfer-Encoding', ['chunked']);
        }

        // Ensure SSL is verified by default
        if (!isset($request['client']['verify'])) {
            $request['client']['verify'] = true;
        }

        $options = [];

        if (isset($request['client'])) {
            foreach ($request['client'] as $key => $value) {
                $method = "add" . ucfirst($key);
                if (isset($methods[$method])) {
                    $this->{$method}($request, $value);
                }
            }
        }

        return $this->createStreamResource(
            $url,
            $request,
            $options,
            $this->context
        );
    }

    /**
     * @param $url
     * @param array $request
     * @param array $options
     * @param resource $context
     * @return resource
     */
    private function createStreamResource(
        $url,
        array $request,
        array $options,
        $context
    ) {

        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return $this->createResource(
            function () use ($url, $context, $request, $options) {

                $errorNo = null;
                $errorMsg = null;

                $resource = @stream_socket_client(
                    $this->entrypoint,
                    $errorNo,
                    $errorMsg,
                    $this->getDefaultTimeout($request),
                    STREAM_CLIENT_CONNECT,
                    $context
                );

                return $resource;
            }
        );
    }

    /**
     * Create a resource and check to ensure it was created successfully
     *
     * @param callable $callback Callable that returns stream resource
     *
     * @return resource
     * @throws \RuntimeException on error
     */
    private function createResource(callable $callback)
    {
        $errors = null;
        set_error_handler(function ($_, $msg, $file, $line) use (&$errors) {
            $errors[] = [
                'message' => $msg,
                'file' => $file,
                'line' => $line
            ];

            return true;
        });

        $resource = $callback();
        restore_error_handler();

        if (!$resource) {
            $message = 'Error creating resource: ';
            if (!empty($errors)) {
                foreach ($errors as $err) {
                    foreach ($err as $key => $value) {
                        $message .= "[$key] $value" . PHP_EOL;
                    }
                }
            }
            throw new RingException(trim($message));
        }

        return $resource;
    }

    /**
     * Creates an error response for the given stream.
     *
     * @param string $url
     * @param RingException $e
     * @return CompletedFutureArray
     */
    private function createErrorResponse($url, RingException $e)
    {
        // Determine if the error was a networking error.
        $message = $e->getMessage();

        // This list can probably get more comprehensive.
        if (strpos($message, 'getaddrinfo') // DNS lookup failed
            || strpos($message, 'Connection refused')
        ) {
            $e = new ConnectException($e->getMessage(), 0, $e);
        }

        return new CompletedFutureArray([
            'status' => null,
            'body' => null,
            'headers' => [],
            'effective_url' => $url,
            'error' => $e
        ]);
    }

    /**
     * @param array $request
     * @param resource $socket
     * @return CompletedFutureArray
     */
    private function createResponse(array $request, $socket)
    {
        // Check if tls is needed
        if ($this->useTls) {
            if (!@stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RingException(sprintf('Cannot enable tls: %s', error_get_last()['message']));
            }
        }

        // Write headers
        $isWrite = $this->fwrite($socket, $this->getRequestHeaderAsString($request));

        // Write body if set
        if ($request['body'] !== null && $isWrite !== false) {
            $stream = Stream::factory($request['body']);
            $filter = null;

            if (Core::header($request, 'Transfer-Encoding') == 'chunked') {
                $filter = stream_filter_prepend($socket, 'chunk', STREAM_FILTER_WRITE);
            }

            while (!$stream->eof() && $isWrite) {
                $isWrite = $this->fwrite($socket, $stream->read(self::CHUNK_SIZE));
            }

            if (Core::header($request, 'Transfer-Encoding') == 'chunked') {
                stream_filter_remove($filter);

                if (false !== $isWrite) {
                    $isWrite = $this->fwrite($socket, "0\r\n\r\n");
                }
            }
        }

        stream_set_timeout($socket, $this->getDefaultTimeout($request));

        // Response should be available, extract headers
        do {
            $response = $this->getResponseWithHeaders($socket, $request);
        } while ($response !== null && $response['status'] == 100);

        // Check timeout
        $metadata = stream_get_meta_data($socket);

        if ($metadata['timed_out']) {
            throw new RingException('Timed out while reading socket');
        }

        if (false === $isWrite) {
            // When an error happen and no response it is most probably due to TLS configuration
            if ($response === null) {
                throw new RingException(
                    'Error while sending request (Broken Pipe' .
                    '), check your TLS configuration and logs in docker daemon for more information ',
                    $request
                );
            }

            throw new RingException('Error while sending request (Broken Pipe)', $request, $response);
        }

        if (null == $response) {
            throw new RingException('No response could be parsed: check server log', $request);
        }

        $this->setResponseStream($response, $socket);

        if (isset($request['client']['wait']) && $request['client']['wait']) {
            Core::body($response);
        }

        return new CompletedFutureArray($response);
    }

    /**
     * @param array $response
     * @param $socket
     */
    private function setResponseStream(array &$response, $socket)
    {
        if (Core::header($response, 'Transfer-Encoding') == "chunked") {
            stream_filter_append($socket, 'dechunk');
        }

        if (isset($response['client']['callback']) && $response['client']['callback']) {
            stream_filter_append($socket, 'event', STREAM_FILTER_READ, [
                'emitter' => $this->emitter,
                'content_type' => Core::header($response, 'Content-Type')
            ]);
        }

        $stream = new Stream($socket);

        if ($response['status'] / 100 > 4) {
            $response['reason'] = (string) $stream;
        }

        $response['body'] = $stream;
    }

    /**
     * Replace fwrite behavior as api is broken in PHP
     *
     * @see https://secure.phabricator.com/rPHU69490c53c9c2ef2002bc2dd4cecfe9a4b080b497
     *
     * @param resource $stream The stream resource
     * @param string|bool $bytes Bytes written in the stream
     *
     * @return bool|int false if pipe is broken, number of bytes written otherwise
     */
    private function fwrite($stream, $bytes)
    {
        if (!strlen($bytes)) {
            return 0;
        }

        $result = @fwrite($stream, $bytes);
        if ($result !== 0) {
            // In cases where some bytes are witten (`$result > 0`) or
            // an error occurs (`$result === false`), the behavior of fwrite() is
            // correct. We can return the value as-is.
            return $result;
        }

        // If we make it here, we performed a 0-length write. Try to distinguish
        // between EAGAIN and EPIPE. To do this, we're going to `stream_select()`
        // the stream, write to it again if PHP claims that it's writable, and
        // consider the pipe broken if the write fails.

        $read = [];
        $write = [$stream];
        $except = [];

        @stream_select($read, $write, $except, 0);

        if (!$write) {
            // The stream isn't writable, so we conclude that it probably really is
            // blocked and the underlying error was EAGAIN. Return 0 to indicate that
            // no data could be written yet.
            return 0;
        }

        // If we make it here, PHP **just** claimed that this stream is writable, so
        // perform a write. If the write also fails, conclude that these failures are
        // EPIPE or some other permanent failure.
        $result = @fwrite($stream, $bytes);
        if ($result !== 0) {
            // The write worked or failed explicitly. This value is fine to return.
            return $result;
        }

        // We performed a 0-length write, were told that the stream was writable, and
        // then immediately performed another 0-length write. Conclude that the pipe
        // is broken and return `false`.
        return false;
    }

    /**
     * @param array $request
     * @return string
     */
    private function getRequestHeaderAsString(array $request)
    {
        $message = vsprintf('%s %s HTTP/%s', [
                strtoupper($request['http_method']),
                $request['url'],
                $request['version']
            ]) . "\r\n";

        foreach ($request['headers'] as $name => $values) {
            $message .= $name . ': ' . implode(', ', $values) . "\r\n";
        }

        $message .= "\r\n";

        return $message;
    }

    /**
     * @param array $request
     * @return null|string
     */
    private function getDefaultTimeout(array $request)
    {
        $timeout = null;

        if (isset($request['timeout'])) {
            $timeout = $request['timeout'];
        }

        if ($timeout === null) {
            if (isset($request['client']['timeout'])) {
                $timeout = $request['client']['timeout'];
            }
        }

        if (null == $timeout) {
            $timeout = ini_get('default_socket_timeout');
        }

        return $timeout;
    }

    private function getResponseWithHeaders($stream, array $request)
    {
        $headers = [];

        while (($line = fgets($stream)) !== false) {
            if (rtrim($line) === '') {
                break;
            }

            $headers[] = trim($line);
        }

        $parts = explode(' ', array_shift($headers), 3);

        if (count($parts) <= 1) {
            return null;
        }

        $options = ['protocol_version' => substr($parts[0], -3)];
        if (isset($parts[2])) {
            $options['reason_phrase'] = $parts[2];
        }

        // Set the size on the stream if it was returned in the response
        $responseHeaders = [];
        foreach ($headers as $header) {
            $headerParts = explode(':', $header, 2);
            $responseHeaders[trim($headerParts[0])] = isset($headerParts[1])
                ? trim($headerParts[1])
                : '';
        }

        $response = [
            'version' => substr($parts[0], 5),
            'status' => $parts[1],
            'reason' => isset($parts[2]) ? $parts[2] : null,
            'headers' => Core::headersFromLines($headers),
            'client' => $request['client']
        ];

        return $response;
    }
}
