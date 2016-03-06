<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NetworkConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\NetworkConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\NetworkConfig) {
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
        $object = new \Docker\API\Model\NetworkConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Bridge')) {
            $object->setBridge($data->{'Bridge'});
        }
        if (property_exists($data, 'Gateway')) {
            $object->setGateway($data->{'Gateway'});
        }
        if (property_exists($data, 'IPAddress')) {
            $object->setIPAddress($data->{'IPAddress'});
        }
        if (property_exists($data, 'IPPrefixLen')) {
            $object->setIPPrefixLen($data->{'IPPrefixLen'});
        }
        if (property_exists($data, 'MacAddress')) {
            $object->setMacAddress($data->{'MacAddress'});
        }
        if (property_exists($data, 'PortMapping')) {
            $object->setPortMapping($data->{'PortMapping'});
        }
        if (property_exists($data, 'Ports')) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Ports'} as $key => $value) {
                $value_1 = $value;
                if (is_array($value)) {
                    $values_1 = [];
                    foreach ($value as $value_2) {
                        $values_1[] = $this->serializer->deserialize($value_2, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
                    }
                    $value_1 = $values_1;
                }
                if (is_null($value)) {
                    $value_1 = $value;
                }
                $values[$key] = $value_1;
            }
            $object->setPorts($values);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getBridge()) {
            $data->{'Bridge'} = $object->getBridge();
        }
        if (null !== $object->getGateway()) {
            $data->{'Gateway'} = $object->getGateway();
        }
        if (null !== $object->getIPAddress()) {
            $data->{'IPAddress'} = $object->getIPAddress();
        }
        if (null !== $object->getIPPrefixLen()) {
            $data->{'IPPrefixLen'} = $object->getIPPrefixLen();
        }
        if (null !== $object->getMacAddress()) {
            $data->{'MacAddress'} = $object->getMacAddress();
        }
        if (null !== $object->getPortMapping()) {
            $data->{'PortMapping'} = $object->getPortMapping();
        }
        if (null !== $object->getPorts()) {
            $values = new \stdClass();
            foreach ($object->getPorts() as $key => $value) {
                $value_1 = $value;
                if (is_array($value)) {
                    $values_1 = [];
                    foreach ($value as $value_2) {
                        $values_1[] = $this->serializer->serialize($value_2, 'raw', $context);
                    }
                    $value_1 = $values_1;
                }
                if (is_null($value)) {
                    $value_1 = $value;
                }
                $values->{$key} = $value_1;
            }
            $data->{'Ports'} = $values;
        }

        return $data;
    }
}
