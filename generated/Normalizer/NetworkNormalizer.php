<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NetworkNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\Network') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\Network) {
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
        $object = new \Docker\API\Model\Network();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Name')) {
            $object->setName($data->{'Name'});
        }
        if (property_exists($data, 'Id')) {
            $object->setId($data->{'Id'});
        }
        if (property_exists($data, 'Scope')) {
            $object->setScope($data->{'Scope'});
        }
        if (property_exists($data, 'Driver')) {
            $object->setDriver($data->{'Driver'});
        }
        if (property_exists($data, 'EnableIPv6')) {
            $object->setEnableIPv6($data->{'EnableIPv6'});
        }
        if (property_exists($data, 'IPAM')) {
            $object->setIPAM($this->serializer->deserialize($data->{'IPAM'}, 'Docker\\API\\Model\\IPAM', 'raw', $context));
        }
        if (property_exists($data, 'Internal')) {
            $object->setInternal($data->{'Internal'});
        }
        if (property_exists($data, 'Containers')) {
            $value = $data->{'Containers'};
            if (is_object($data->{'Containers'})) {
                $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'Containers'} as $key => $value_1) {
                    $values[$key] = $this->serializer->deserialize($value_1, 'Docker\\API\\Model\\NetworkContainer', 'raw', $context);
                }
                $value = $values;
            }
            if (is_null($data->{'Containers'})) {
                $value = $data->{'Containers'};
            }
            $object->setContainers($value);
        }
        if (property_exists($data, 'Options')) {
            $values_1 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Options'} as $key_1 => $value_2) {
                $values_1[$key_1] = $value_2;
            }
            $object->setOptions($values_1);
        }
        if (property_exists($data, 'Labels')) {
            $value_3 = $data->{'Labels'};
            if (is_object($data->{'Labels'})) {
                $values_2 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'Labels'} as $key_2 => $value_4) {
                    $values_2[$key_2] = $value_4;
                }
                $value_3 = $values_2;
            }
            if (is_null($data->{'Labels'})) {
                $value_3 = $data->{'Labels'};
            }
            $object->setLabels($value_3);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getId()) {
            $data->{'Id'} = $object->getId();
        }
        if (null !== $object->getScope()) {
            $data->{'Scope'} = $object->getScope();
        }
        if (null !== $object->getDriver()) {
            $data->{'Driver'} = $object->getDriver();
        }
        if (null !== $object->getEnableIPv6()) {
            $data->{'EnableIPv6'} = $object->getEnableIPv6();
        }
        if (null !== $object->getIPAM()) {
            $data->{'IPAM'} = $this->serializer->serialize($object->getIPAM(), 'raw', $context);
        }
        if (null !== $object->getInternal()) {
            $data->{'Internal'} = $object->getInternal();
        }
        $value = $object->getContainers();
        if (is_object($object->getContainers())) {
            $values = new \stdClass();
            foreach ($object->getContainers() as $key => $value_1) {
                $values->{$key} = $this->serializer->serialize($value_1, 'raw', $context);
            }
            $value = $values;
        }
        if (is_null($object->getContainers())) {
            $value = $object->getContainers();
        }
        $data->{'Containers'} = $value;
        if (null !== $object->getOptions()) {
            $values_1 = new \stdClass();
            foreach ($object->getOptions() as $key_1 => $value_2) {
                $values_1->{$key_1} = $value_2;
            }
            $data->{'Options'} = $values_1;
        }
        $value_3 = $object->getLabels();
        if (is_object($object->getLabels())) {
            $values_2 = new \stdClass();
            foreach ($object->getLabels() as $key_2 => $value_4) {
                $values_2->{$key_2} = $value_4;
            }
            $value_3 = $values_2;
        }
        if (is_null($object->getLabels())) {
            $value_3 = $object->getLabels();
        }
        $data->{'Labels'} = $value_3;

        return $data;
    }
}
