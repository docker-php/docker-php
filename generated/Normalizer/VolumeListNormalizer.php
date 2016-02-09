<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class VolumeListNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\VolumeList') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\VolumeList) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (empty($data)) {
            return null;
        }
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\VolumeList();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Volumes'})) {
            $values_231 = [];
            foreach ($data->{'Volumes'} as $value_232) {
                $values_231[] = $this->serializer->deserialize($value_232, 'Docker\\API\\Model\\Volume', 'raw', $context);
            }
            $object->setVolumes($values_231);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getVolumes()) {
            $values_233 = [];
            foreach ($object->getVolumes() as $value_234) {
                $values_233[] = $this->serializer->serialize($value_234, 'raw', $context);
            }
            $data->{'Volumes'} = $values_233;
        }

        return $data;
    }
}
