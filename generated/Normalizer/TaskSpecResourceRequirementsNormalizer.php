<?php

namespace Docker\API\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class TaskSpecResourceRequirementsNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\TaskSpecResourceRequirements') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\TaskSpecResourceRequirements) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $object = new \Docker\API\Model\TaskSpecResourceRequirements();
        if (property_exists($data, 'Limits')) {
            $object->setLimits($this->serializer->deserialize($data->{'Limits'}, 'Docker\\API\\Model\\NodeResources', 'raw', $context));
        }
        if (property_exists($data, 'Reservations')) {
            $object->setReservations($this->serializer->deserialize($data->{'Reservations'}, 'Docker\\API\\Model\\NodeResources', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getLimits()) {
            $data->{'Limits'} = $this->serializer->serialize($object->getLimits(), 'raw', $context);
        }
        if (null !== $object->getReservations()) {
            $data->{'Reservations'} = $this->serializer->serialize($object->getReservations(), 'raw', $context);
        }

        return $data;
    }
}
