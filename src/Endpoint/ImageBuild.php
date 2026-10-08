<?php

declare(strict_types=1);

namespace Docker\Endpoint;

use Docker\API\Endpoint\ImageBuild as BaseEndpoint;
use Docker\Stream\BuildStream;
use Docker\Stream\TarStream;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\SerializerInterface;

class ImageBuild extends BaseEndpoint
{
    public function getBody(SerializerInterface $serializer, $streamFactory = null): array
    {
        $body = $this->body;

        if (\is_resource($body)) {
            $body = new TarStream(Stream::create($body));
        }

        return [['Content-Type' => ['application/octet-stream']], $body];
    }

    protected function getQueryOptionsResolver(): OptionsResolver
    {
        $optionsResolver = parent::getQueryOptionsResolver();
        // Docker applies each repeated `t` parameter as a separate tag.
        $optionsResolver->setAllowedTypes('t', ['string', 'string[]']);
        $optionsResolver->setNormalizer('t', static fn (Options $options, $value) => \is_array($value) ? array_values($value) : $value);

        return $optionsResolver;
    }

    protected function getQueryStyles(): array
    {
        return ['t' => ['style' => 'form', 'explode' => true]] + parent::getQueryStyles();
    }

    protected function transformResponseBody(ResponseInterface $response, SerializerInterface $serializer, ?string $contentType = null)
    {
        if (200 === $response->getStatusCode()) {
            return new BuildStream($response->getBody(), $serializer);
        }

        return parent::transformResponseBody($response, $serializer, $contentType);
    }
}
