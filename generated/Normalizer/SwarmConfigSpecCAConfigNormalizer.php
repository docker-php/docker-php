<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class SwarmConfigSpecCAConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\SwarmConfigSpecCAConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\SwarmConfigSpecCAConfig) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\SwarmConfigSpecCAConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'NodeCertExpiry')) {
            $object->setNodeCertExpiry($data->{'NodeCertExpiry'});
        }
        if (property_exists($data, 'ExternalCA')) {
            $object->setExternalCA($this->serializer->deserialize($data->{'ExternalCA'}, 'Docker\\API\\Model\\SwarmConfigSpecCAConfigExternalCA', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getNodeCertExpiry()) {
            $data->{'NodeCertExpiry'} = $object->getNodeCertExpiry();
        }
        if (null !== $object->getExternalCA()) {
            $data->{'ExternalCA'} = $this->serializer->serialize($object->getExternalCA(), 'raw', $context);
        }

        return $data;
    }
}
