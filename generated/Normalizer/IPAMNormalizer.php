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
            $values_217 = [];
            foreach ($data->{'Config'} as $value_218) {
                $values_219 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($value_218 as $key_221 => $value_220) {
                    $values_219[$key_221] = $value_220;
                }
                $values_217[] = $values_219;
            }
            $object->setConfig($values_217);
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
            $values_222 = [];
            foreach ($object->getConfig() as $value_223) {
                $values_224 = new \stdClass();
                foreach ($value_223 as $key_226 => $value_225) {
                    $values_224->{$key_226} = $value_225;
                }
                $values_222[] = $values_224;
            }
            $data->{'Config'} = $values_222;
        }

        return $data;
    }
}
