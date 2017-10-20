<?php

namespace Docker\API\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NodeResourcesNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\NodeResources') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\NodeResources) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $object = new \Docker\API\Model\NodeResources();
        if (property_exists($data, 'NanoCPUs')) {
            $object->setNanoCPUs($data->{'NanoCPUs'});
        }
        if (property_exists($data, 'MemoryBytes')) {
            $object->setMemoryBytes($data->{'MemoryBytes'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getNanoCPUs()) {
            $data->{'NanoCPUs'} = $object->getNanoCPUs();
        }
        if (null !== $object->getMemoryBytes()) {
            $data->{'MemoryBytes'} = $object->getMemoryBytes();
        }

        return $data;
    }
}
