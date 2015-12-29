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
            $values_161 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'IndexConfigs'} as $key_163 => $value_162) {
                $values_161[$key_163] = $this->serializer->deserialize($value_162, 'Docker\\API\\Model\\Registry', 'raw', $context);
            }
            $object->setIndexConfigs($values_161);
        }
        if (isset($data->{'InsecureRegistryCIDRs'})) {
            $values_164 = [];
            foreach ($data->{'InsecureRegistryCIDRs'} as $value_165) {
                $values_164[] = $value_165;
            }
            $object->setInsecureRegistryCIDRs($values_164);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getIndexConfigs()) {
            $values_166 = new \stdClass();
            foreach ($object->getIndexConfigs() as $key_168 => $value_167) {
                $values_166->{$key_168} = $this->serializer->serialize($value_167, 'raw', $context);
            }
            $data->{'IndexConfigs'} = $values_166;
        }
        if (null !== $object->getInsecureRegistryCIDRs()) {
            $values_169 = [];
            foreach ($object->getInsecureRegistryCIDRs() as $value_170) {
                $values_169[] = $value_170;
            }
            $data->{'InsecureRegistryCIDRs'} = $values_169;
        }

        return $data;
    }
}
