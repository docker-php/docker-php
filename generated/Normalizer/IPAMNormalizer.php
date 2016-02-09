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
            $values_253 = [];
            foreach ($data->{'Config'} as $value_254) {
                $values_253[] = $this->serializer->deserialize($value_254, 'Docker\\API\\Model\\IPAMConfig', 'raw', $context);
            }
            $object->setConfig($values_253);
        }
        if (isset($data->{'Options'})) {
            $values_255 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Options'} as $key_257 => $value_256) {
                $values_255[$key_257] = $value_256;
            }
            $object->setOptions($values_255);
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
            $values_258 = [];
            foreach ($object->getConfig() as $value_259) {
                $values_258[] = $this->serializer->serialize($value_259, 'raw', $context);
            }
            $data->{'Config'} = $values_258;
        }
        if (null !== $object->getOptions()) {
            $values_260 = new \stdClass();
            foreach ($object->getOptions() as $key_262 => $value_261) {
                $values_260->{$key_262} = $value_261;
            }
            $data->{'Options'} = $values_260;
        }

        return $data;
    }
}
