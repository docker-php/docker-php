<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class TaskSpecNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\TaskSpec') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\TaskSpec) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\TaskSpec();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'ContainerSpec')) {
            $object->setContainerSpec($this->serializer->deserialize($data->{'ContainerSpec'}, 'Docker\\API\\Model\\ContainerSpec', 'raw', $context));
        }
        if (property_exists($data, 'Resources')) {
            $object->setResources($this->serializer->deserialize($data->{'Resources'}, 'Docker\\API\\Model\\TaskSpecResourceRequirements', 'raw', $context));
        }
        if (property_exists($data, 'RestartPolicy')) {
            $object->setRestartPolicy($this->serializer->deserialize($data->{'RestartPolicy'}, 'Docker\\API\\Model\\TaskSpecRestartPolicy', 'raw', $context));
        }
        if (property_exists($data, 'Placement')) {
            $object->setPlacement($this->serializer->deserialize($data->{'Placement'}, 'Docker\\API\\Model\\TaskSpecPlacement', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getContainerSpec()) {
            $data->{'ContainerSpec'} = $this->serializer->serialize($object->getContainerSpec(), 'raw', $context);
        }
        if (null !== $object->getResources()) {
            $data->{'Resources'} = $this->serializer->serialize($object->getResources(), 'raw', $context);
        }
        if (null !== $object->getRestartPolicy()) {
            $data->{'RestartPolicy'} = $this->serializer->serialize($object->getRestartPolicy(), 'raw', $context);
        }
        if (null !== $object->getPlacement()) {
            $data->{'Placement'} = $this->serializer->serialize($object->getPlacement(), 'raw', $context);
        }

        return $data;
    }
}
