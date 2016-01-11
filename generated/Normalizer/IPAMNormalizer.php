<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class IPAMNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\IPAM') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\IPAM) {
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
        $object = new \Docker\API\Model\IPAM();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Driver'})) {
            $object->setDriver($data->{'Driver'});
        }
        if (isset($data->{'Config'})) {
            $values_209 = [];
            foreach ($data->{'Config'} as $value_210) {
                $values_211 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($value_210 as $key_213 => $value_212) {
                    $values_211[$key_213] = $value_212;
                }
                $values_209[] = $values_211;
            }
            $object->setConfig($values_209);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getDriver()) {
            $data->{'Driver'} = $object->getDriver();
        }
        if (null !== $object->getConfig()) {
            $values_214 = [];
            foreach ($object->getConfig() as $value_215) {
                $values_216 = new \stdClass();
                foreach ($value_215 as $key_218 => $value_217) {
                    $values_216->{$key_218} = $value_217;
                }
                $values_214[] = $values_216;
            }
            $data->{'Config'} = $values_214;
        }

        return $data;
    }
}
