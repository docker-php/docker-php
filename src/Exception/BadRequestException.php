<?php

declare(strict_types=1);

namespace Docker\Exception;

use Psr\Http\Message\ResponseInterface;

final class BadRequestException extends \Docker\API\Exception\BadRequestException
{
    public function __construct(string $message, private ResponseInterface $response)
    {
        parent::__construct($message);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
