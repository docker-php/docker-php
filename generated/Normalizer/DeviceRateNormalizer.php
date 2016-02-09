<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class DeviceRateNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\DeviceRate') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\DeviceRate) {
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
        $object = new \Docker\API\Model\DeviceRate();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Path'})) {
            $object->setPath($data->{'Path'});
        }
        if (isset($data->{'Rate'})) {
            $value_79 = $data->{'Rate'};
            if (is_int($data->{'Rate'})) {
                $value_79 = $data->{'Rate'};
            }
            if (is_string($data->{'Rate'})) {
                $value_79 = $data->{'Rate'};
            }
            $object->setRate($value_79);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getPath()) {
            $data->{'Path'} = $object->getPath();
        }
        if (null !== $object->getRate()) {
            $value_80 = $object->getRate();
            if (is_int($object->getRate())) {
                $value_80 = $object->getRate();
            }
            if (is_string($object->getRate())) {
                $value_80 = $object->getRate();
            }
            $data->{'Rate'} = $value_80;
        }

        return $data;
    }
}
