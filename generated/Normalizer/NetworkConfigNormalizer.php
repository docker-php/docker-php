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
        if (isset($data->{'Bridge'})) {
            $object->setBridge($data->{'Bridge'});
        }
        if (isset($data->{'Gateway'})) {
            $object->setGateway($data->{'Gateway'});
        }
        if (isset($data->{'IPAddress'})) {
            $object->setIPAddress($data->{'IPAddress'});
        }
        if (isset($data->{'IPPrefixLen'})) {
            $object->setIPPrefixLen($data->{'IPPrefixLen'});
        }
        if (isset($data->{'MacAddress'})) {
            $object->setMacAddress($data->{'MacAddress'});
        }
        if (isset($data->{'PortMapping'})) {
            $object->setPortMapping($data->{'PortMapping'});
        }
        if (isset($data->{'Ports'})) {
            $values_99 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Ports'} as $key_101 => $value_100) {
                $value_102 = $value_100;
                if (is_array($value_100)) {
                    $values_103 = [];
                    foreach ($value_100 as $value_104) {
                        $values_103[] = $this->serializer->deserialize($value_104, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
                    }
                    $value_102 = $values_103;
                }
                if (is_null($value_100)) {
                    $value_102 = $value_100;
                }
                $values_99[$key_101] = $value_102;
            }
            $object->setPorts($values_99);
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
            $values_105 = new \stdClass();
            foreach ($object->getPorts() as $key_107 => $value_106) {
                $value_108 = $value_106;
                if (is_array($value_106)) {
                    $values_109 = [];
                    foreach ($value_106 as $value_110) {
                        $values_109[] = $this->serializer->serialize($value_110, 'raw', $context);
                    }
                    $value_108 = $values_109;
                }
                if (is_null($value_106)) {
                    $value_108 = $value_106;
                }
                $values_105->{$key_107} = $value_108;
            }
            $data->{'Ports'} = $values_105;
        }

        return $data;
    }
}
