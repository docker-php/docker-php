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
            $values_241 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Containers'} as $key_243 => $value_242) {
                $values_241[$key_243] = $this->serializer->deserialize($value_242, 'Docker\\API\\Model\\NetworkContainer', 'raw', $context);
            }
            $object->setContainers($values_241);
        }
        if (isset($data->{'Options'})) {
            $values_244 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Options'} as $key_246 => $value_245) {
                $values_244[$key_246] = $value_245;
            }
            $object->setOptions($values_244);
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
            $values_247 = new \stdClass();
            foreach ($object->getContainers() as $key_249 => $value_248) {
                $values_247->{$key_249} = $this->serializer->serialize($value_248, 'raw', $context);
            }
            $data->{'Containers'} = $values_247;
        }
        if (null !== $object->getOptions()) {
            $values_250 = new \stdClass();
            foreach ($object->getOptions() as $key_252 => $value_251) {
                $values_250->{$key_252} = $value_251;
            }
            $data->{'Options'} = $values_250;
        }

        return $data;
    }
}
