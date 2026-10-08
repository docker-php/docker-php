<?php

declare(strict_types=1);

namespace Docker\Exception;

use Docker\API\Exception\ApiException;
use Docker\API\Exception\WithResponseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Docker returned an error status that the generated endpoint does not handle.
 *
 * Thrown unless Docker::throwOnUnexpectedStatus(false) is set; catch
 * UnexpectedClientErrorException or UnexpectedServerErrorException to tell 4xx
 * and 5xx responses apart.
 */
abstract class UnexpectedStatusCodeException extends \RuntimeException implements ApiException, WithResponseInterface
{
    final public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly string $errorMessage,
        private readonly ResponseInterface $response,
    ) {
        $status = $response->getStatusCode();
        parent::__construct(
            \sprintf('Docker returned HTTP %d for %s %s: %s', $status, $method, $path, $errorMessage),
            $status
        );
    }

    /**
     * Create the client or server error exception for an unhandled response.
     *
     * The response body must be seekable; it is rewound after reading Docker's
     * error message.
     */
    public static function fromResponse(string $method, string $path, ResponseInterface $response): self
    {
        $body = $response->getBody();
        $body->rewind();
        $raw = $body->getContents();
        $body->rewind();

        $decoded = json_decode($raw, true);
        $message = \is_array($decoded) && \is_string($decoded['message'] ?? null) ? $decoded['message'] : trim($raw);
        if ('' === $message) {
            $message = $response->getReasonPhrase() ?: 'no error message';
        }
        if (\strlen($message) > 500) {
            $message = substr($message, 0, 500).'...';
        }

        return $response->getStatusCode() >= 500
            ? new UnexpectedServerErrorException($method, $path, $message, $response)
            : new UnexpectedClientErrorException($method, $path, $message, $response);
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /** Docker's error message, or the response body when it is not JSON. */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    /** Request path, without the API version prefix or query string. */
    public function getPath(): string
    {
        return $this->path;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
