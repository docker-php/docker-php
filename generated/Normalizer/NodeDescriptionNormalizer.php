<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NodeDescriptionNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\NodeDescription') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\NodeDescription) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\NodeDescription();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Hostname')) {
            $object->setHostname($data->{'Hostname'});
        }
        if (property_exists($data, 'Platform')) {
            $object->setPlatform($this->serializer->deserialize($data->{'Platform'}, 'Docker\\API\\Model\\NodePlatform', 'raw', $context));
        }
        if (property_exists($data, 'Resources')) {
            $object->setResources($this->serializer->deserialize($data->{'Resources'}, 'Docker\\API\\Model\\NodeResources', 'raw', $context));
        }
        if (property_exists($data, 'Engine')) {
            $object->setEngine($this->serializer->deserialize($data->{'Engine'}, 'Docker\\API\\Model\\NodeEngine', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getHostname()) {
            $data->{'Hostname'} = $object->getHostname();
        }
        if (null !== $object->getPlatform()) {
            $data->{'Platform'} = $this->serializer->serialize($object->getPlatform(), 'raw', $context);
        }
        if (null !== $object->getResources()) {
            $data->{'Resources'} = $this->serializer->serialize($object->getResources(), 'raw', $context);
        }
        if (null !== $object->getEngine()) {
            $data->{'Engine'} = $this->serializer->serialize($object->getEngine(), 'raw', $context);
        }

        return $data;
    }
}
