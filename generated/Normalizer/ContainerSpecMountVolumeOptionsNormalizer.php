<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ContainerSpecMountVolumeOptionsNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ContainerSpecMountVolumeOptions') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ContainerSpecMountVolumeOptions) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\ContainerSpecMountVolumeOptions();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'NoCopy')) {
            $object->setNoCopy($data->{'NoCopy'});
        }
        if (property_exists($data, 'Labels')) {
            $value = $data->{'Labels'};
            if (is_object($data->{'Labels'})) {
                $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'Labels'} as $key => $value_1) {
                    $values[$key] = $value_1;
                }
                $value = $values;
            }
            if (is_null($data->{'Labels'})) {
                $value = $data->{'Labels'};
            }
            $object->setLabels($value);
        }
        if (property_exists($data, 'DriverConfig')) {
            $object->setDriverConfig($this->serializer->deserialize($data->{'DriverConfig'}, 'Docker\\API\\Model\\Driver', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getNoCopy()) {
            $data->{'NoCopy'} = $object->getNoCopy();
        }
        $value = $object->getLabels();
        if (is_object($object->getLabels())) {
            $values = new \stdClass();
            foreach ($object->getLabels() as $key => $value_1) {
                $values->{$key} = $value_1;
            }
            $value = $values;
        }
        if (is_null($object->getLabels())) {
            $value = $object->getLabels();
        }
        $data->{'Labels'} = $value;
        if (null !== $object->getDriverConfig()) {
            $data->{'DriverConfig'} = $this->serializer->serialize($object->getDriverConfig(), 'raw', $context);
        }

        return $data;
    }
}
