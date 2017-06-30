<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ServiceSpecModeNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ServiceSpecMode') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ServiceSpecMode) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\ServiceSpecMode();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Replicated')) {
            $object->setReplicated($this->serializer->deserialize($data->{'Replicated'}, 'Docker\\API\\Model\\ReplicatedService', 'raw', $context));
        }
        if (property_exists($data, 'Global')) {
            $object->setGlobal($this->serializer->deserialize($data->{'Global'}, 'Docker\\API\\Model\\GlobalService', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getReplicated()) {
            $data->{'Replicated'} = $this->serializer->serialize($object->getReplicated(), 'raw', $context);
        }
        if (null !== $object->getGlobal()) {
            $data->{'Global'} = $this->serializer->serialize($object->getGlobal(), 'raw', $context);
        }

        return $data;
    }
}
