<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class TaskStatusNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\TaskStatus') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\TaskStatus) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\TaskStatus();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Timestamp')) {
            $object->setTimestamp($data->{'Timestamp'});
        }
        if (property_exists($data, 'State')) {
            $object->setState($data->{'State'});
        }
        if (property_exists($data, 'Message')) {
            $object->setMessage($data->{'Message'});
        }
        if (property_exists($data, 'Err')) {
            $object->setErr($data->{'Err'});
        }
        if (property_exists($data, 'ContainerStatus')) {
            $object->setContainerStatus($this->serializer->deserialize($data->{'ContainerStatus'}, 'Docker\\API\\Model\\ContainerStatus', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getTimestamp()) {
            $data->{'Timestamp'} = $object->getTimestamp();
        }
        if (null !== $object->getState()) {
            $data->{'State'} = $object->getState();
        }
        if (null !== $object->getMessage()) {
            $data->{'Message'} = $object->getMessage();
        }
        if (null !== $object->getErr()) {
            $data->{'Err'} = $object->getErr();
        }
        if (null !== $object->getContainerStatus()) {
            $data->{'ContainerStatus'} = $this->serializer->serialize($object->getContainerStatus(), 'raw', $context);
        }

        return $data;
    }
}
