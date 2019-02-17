<?php

declare(strict_types=1);

namespace ElevenLabs\Docker\Client;

namespace Docker\Client;

interface ProvideAmpArtaxClientOptions
{
    /**
     * Return a list of options for the Artax client.
     */
    public function getAmpArtaxClientOptions(): array;
}
