<?php

declare(strict_types=1);

namespace Docker\Endpoint;

use Docker\API\Endpoint\ExecStart;

/** @internal */
final class InteractiveExecStart extends ExecStart
{
    public function getExtraHeaders(): array
    {
        return parent::getExtraHeaders() + [
            'Connection' => ['Upgrade'],
            'Upgrade' => ['tcp'],
        ];
    }
}
