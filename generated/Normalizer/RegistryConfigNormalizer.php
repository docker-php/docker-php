<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class RegistryConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\RegistryConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\RegistryConfig) {
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
        $object = new \Docker\API\Model\RegistryConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'IndexConfigs')) {
            $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'IndexConfigs'} as $key => $value) {
                $values[$key] = $this->serializer->deserialize($value, 'Docker\\API\\Model\\Registry', 'raw', $context);
            }
            $object->setIndexConfigs($values);
        }
        if (property_exists($data, 'InsecureRegistryCIDRs')) {
            $values_1 = [];
            foreach ($data->{'InsecureRegistryCIDRs'} as $value_1) {
                $values_1[] = $value_1;
            }
            $object->setInsecureRegistryCIDRs($values_1);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getIndexConfigs()) {
            $values = new \stdClass();
            foreach ($object->getIndexConfigs() as $key => $value) {
                $values->{$key} = $this->serializer->serialize($value, 'raw', $context);
            }
            $data->{'IndexConfigs'} = $values;
        }
        if (null !== $object->getInsecureRegistryCIDRs()) {
            $values_1 = [];
            foreach ($object->getInsecureRegistryCIDRs() as $value_1) {
                $values_1[] = $value_1;
            }
            $data->{'InsecureRegistryCIDRs'} = $values_1;
        }

        return $data;
    }
}
