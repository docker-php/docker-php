<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class PortConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\PortConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\PortConfig) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\PortConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Name')) {
            $object->setName($data->{'Name'});
        }
        if (property_exists($data, 'Protocol')) {
            $object->setProtocol($data->{'Protocol'});
        }
        if (property_exists($data, 'TargetPort')) {
            $object->setTargetPort($data->{'TargetPort'});
        }
        if (property_exists($data, 'PublishedPort')) {
            $object->setPublishedPort($data->{'PublishedPort'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getProtocol()) {
            $data->{'Protocol'} = $object->getProtocol();
        }
        if (null !== $object->getTargetPort()) {
            $data->{'TargetPort'} = $object->getTargetPort();
        }
        if (null !== $object->getPublishedPort()) {
            $data->{'PublishedPort'} = $object->getPublishedPort();
        }

        return $data;
    }
}
