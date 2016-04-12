<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class IPAMNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\IPAM') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\IPAM) {
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
        $object = new \Docker\API\Model\IPAM();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Driver')) {
            $object->setDriver($data->{'Driver'});
        }
        if (property_exists($data, 'Config')) {
            $value = $data->{'Config'};
            if (is_array($data->{'Config'})) {
                $values = [];
                foreach ($data->{'Config'} as $value_1) {
                    $values_1 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                    foreach ($value_1 as $key => $value_2) {
                        $values_1[$key] = $value_2;
                    }
                    $values[] = $values_1;
                }
                $value = $values;
            }
            if (is_null($data->{'Config'})) {
                $value = $data->{'Config'};
            }
            $object->setConfig($value);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getDriver()) {
            $data->{'Driver'} = $object->getDriver();
        }
        $value = $object->getConfig();
        if (is_array($object->getConfig())) {
            $values = [];
            foreach ($object->getConfig() as $value_1) {
                $values_1 = new \stdClass();
                foreach ($value_1 as $key => $value_2) {
                    $values_1->{$key} = $value_2;
                }
                $values[] = $values_1;
            }
            $value = $values;
        }
        if (is_null($object->getConfig())) {
            $value = $object->getConfig();
        }
        $data->{'Config'} = $value;

        return $data;
    }
}
