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
        if (isset($data->{'Networks'})) {
            $values_121 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Networks'} as $key_123 => $value_122) {
                $values_121[$key_123] = $this->serializer->deserialize($value_122, 'Docker\\API\\Model\\ContainerNetwork', 'raw', $context);
            }
            $object->setNetworks($values_121);
        }
        if (isset($data->{'Ports'})) {
            $values_124 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Ports'} as $key_126 => $value_125) {
                $value_127 = $value_125;
                if (is_array($value_125)) {
                    $values_128 = [];
                    foreach ($value_125 as $value_129) {
                        $values_128[] = $this->serializer->deserialize($value_129, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
                    }
                    $value_127 = $values_128;
                }
                if (is_null($value_125)) {
                    $value_127 = $value_125;
                }
                $values_124[$key_126] = $value_127;
            }
            $object->setPorts($values_124);
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
        if (null !== $object->getNetworks()) {
            $values_130 = new \stdClass();
            foreach ($object->getNetworks() as $key_132 => $value_131) {
                $values_130->{$key_132} = $this->serializer->serialize($value_131, 'raw', $context);
            }
            $data->{'Networks'} = $values_130;
        }
        if (null !== $object->getPorts()) {
            $values_133 = new \stdClass();
            foreach ($object->getPorts() as $key_135 => $value_134) {
                $value_136 = $value_134;
                if (is_array($value_134)) {
                    $values_137 = [];
                    foreach ($value_134 as $value_138) {
                        $values_137[] = $this->serializer->serialize($value_138, 'raw', $context);
                    }
                    $value_136 = $values_137;
                }
                if (is_null($value_134)) {
                    $value_136 = $value_134;
                }
                $values_133->{$key_135} = $value_136;
            }
            $data->{'Ports'} = $values_133;
        }

        return $data;
    }
}
