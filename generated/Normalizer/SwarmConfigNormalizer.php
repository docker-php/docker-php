<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class SwarmConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\SwarmConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\SwarmConfig) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\SwarmConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'ListenAddr')) {
            $object->setListenAddr($data->{'ListenAddr'});
        }
        if (property_exists($data, 'AdvertiseAddr')) {
            $object->setAdvertiseAddr($data->{'AdvertiseAddr'});
        }
        if (property_exists($data, 'ForceNewCluster')) {
            $object->setForceNewCluster($data->{'ForceNewCluster'});
        }
        if (property_exists($data, 'Spec')) {
            $object->setSpec($this->serializer->deserialize($data->{'Spec'}, 'Docker\\API\\Model\\SwarmConfigSpec', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getListenAddr()) {
            $data->{'ListenAddr'} = $object->getListenAddr();
        }
        if (null !== $object->getAdvertiseAddr()) {
            $data->{'AdvertiseAddr'} = $object->getAdvertiseAddr();
        }
        if (null !== $object->getForceNewCluster()) {
            $data->{'ForceNewCluster'} = $object->getForceNewCluster();
        }
        if (null !== $object->getSpec()) {
            $data->{'Spec'} = $this->serializer->serialize($object->getSpec(), 'raw', $context);
        }

        return $data;
    }
}
