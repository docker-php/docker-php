<?php

namespace Docker\API\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ContainerDisconnectNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ContainerDisconnect') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ContainerDisconnect) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $object = new \Docker\API\Model\ContainerDisconnect();
        if (property_exists($data, 'Container')) {
            $object->setContainer($data->{'Container'});
        }
        if (property_exists($data, 'Force')) {
            $object->setForce($data->{'Force'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getContainer()) {
            $data->{'Container'} = $object->getContainer();
        }
        if (null !== $object->getForce()) {
            $data->{'Force'} = $object->getForce();
        }

        return $data;
    }
}
