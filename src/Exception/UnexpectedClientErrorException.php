<?php

declare(strict_types=1);

namespace Docker\Exception;

use Docker\API\Exception\ClientException;

/**
 * Docker returned a 4xx status that the generated endpoint does not handle.
 */
final class UnexpectedClientErrorException extends UnexpectedStatusCodeException implements ClientException
{
}
