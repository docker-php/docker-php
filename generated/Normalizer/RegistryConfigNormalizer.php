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
            $values_205 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'IndexConfigs'} as $key_207 => $value_206) {
                $values_205[$key_207] = $this->serializer->deserialize($value_206, 'Docker\\API\\Model\\Registry', 'raw', $context);
            }
            $object->setIndexConfigs($values_205);
        }
        if (isset($data->{'InsecureRegistryCIDRs'})) {
            $values_208 = [];
            foreach ($data->{'InsecureRegistryCIDRs'} as $value_209) {
                $values_208[] = $value_209;
            }
            $object->setInsecureRegistryCIDRs($values_208);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getIndexConfigs()) {
            $values_210 = new \stdClass();
            foreach ($object->getIndexConfigs() as $key_212 => $value_211) {
                $values_210->{$key_212} = $this->serializer->serialize($value_211, 'raw', $context);
            }
            $data->{'IndexConfigs'} = $values_210;
        }
        if (null !== $object->getInsecureRegistryCIDRs()) {
            $values_213 = [];
            foreach ($object->getInsecureRegistryCIDRs() as $value_214) {
                $values_213[] = $value_214;
            }
            $data->{'InsecureRegistryCIDRs'} = $values_213;
        }

        return $data;
    }
}
