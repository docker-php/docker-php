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
        if (isset($data->{'Name'})) {
            $object->setName($data->{'Name'});
        }
        if (isset($data->{'Id'})) {
            $object->setId($data->{'Id'});
        }
        if (isset($data->{'Scope'})) {
            $object->setScope($data->{'Scope'});
        }
        if (isset($data->{'Driver'})) {
            $object->setDriver($data->{'Driver'});
        }
        if (isset($data->{'IPAM'})) {
            $object->setIPAM($this->serializer->deserialize($data->{'IPAM'}, 'Docker\\API\\Model\\IPAM', 'raw', $context));
        }
        if (isset($data->{'Containers'})) {
            $values_197 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Containers'} as $key_199 => $value_198) {
                $values_197[$key_199] = $this->serializer->deserialize($value_198, 'Docker\\API\\Model\\NetworkContainer', 'raw', $context);
            }
            $object->setContainers($values_197);
        }
        if (isset($data->{'Options'})) {
            $values_200 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Options'} as $key_202 => $value_201) {
                $values_200[$key_202] = $value_201;
            }
            $object->setOptions($values_200);
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
        if (null !== $object->getIPAM()) {
            $data->{'IPAM'} = $this->serializer->serialize($object->getIPAM(), 'raw', $context);
        }
        if (null !== $object->getContainers()) {
            $values_203 = new \stdClass();
            foreach ($object->getContainers() as $key_205 => $value_204) {
                $values_203->{$key_205} = $this->serializer->serialize($value_204, 'raw', $context);
            }
            $data->{'Containers'} = $values_203;
        }
        if (null !== $object->getOptions()) {
            $values_206 = new \stdClass();
            foreach ($object->getOptions() as $key_208 => $value_207) {
                $values_206->{$key_208} = $value_207;
            }
            $data->{'Options'} = $values_206;
        }

        return $data;
    }
}
