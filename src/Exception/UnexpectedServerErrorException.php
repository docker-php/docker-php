<?php

declare(strict_types=1);

namespace Docker\Exception;

use Docker\API\Exception\ServerException;

/**
 * Docker returned a 5xx status that the generated endpoint does not handle.
 */
final class UnexpectedServerErrorException extends UnexpectedStatusCodeException implements ServerException
{
}
