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
        if (isset($data->{'IndexConfigs'})) {
            $values_169 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'IndexConfigs'} as $key_171 => $value_170) {
                $values_169[$key_171] = $this->serializer->deserialize($value_170, 'Docker\\API\\Model\\Registry', 'raw', $context);
            }
            $object->setIndexConfigs($values_169);
        }
        if (isset($data->{'InsecureRegistryCIDRs'})) {
            $values_172 = [];
            foreach ($data->{'InsecureRegistryCIDRs'} as $value_173) {
                $values_172[] = $value_173;
            }
            $object->setInsecureRegistryCIDRs($values_172);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getIndexConfigs()) {
            $values_174 = new \stdClass();
            foreach ($object->getIndexConfigs() as $key_176 => $value_175) {
                $values_174->{$key_176} = $this->serializer->serialize($value_175, 'raw', $context);
            }
            $data->{'IndexConfigs'} = $values_174;
        }
        if (null !== $object->getInsecureRegistryCIDRs()) {
            $values_177 = [];
            foreach ($object->getInsecureRegistryCIDRs() as $value_178) {
                $values_177[] = $value_178;
            }
            $data->{'InsecureRegistryCIDRs'} = $values_177;
        }

        return $data;
    }
}
