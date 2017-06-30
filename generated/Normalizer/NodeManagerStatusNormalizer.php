<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NodeManagerStatusNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\NodeManagerStatus') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\NodeManagerStatus) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\NodeManagerStatus();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Leader')) {
            $object->setLeader($data->{'Leader'});
        }
        if (property_exists($data, 'Reachability')) {
            $object->setReachability($data->{'Reachability'});
        }
        if (property_exists($data, 'Addr')) {
            $object->setAddr($data->{'Addr'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getLeader()) {
            $data->{'Leader'} = $object->getLeader();
        }
        if (null !== $object->getReachability()) {
            $data->{'Reachability'} = $object->getReachability();
        }
        if (null !== $object->getAddr()) {
            $data->{'Addr'} = $object->getAddr();
        }

        return $data;
    }
}
