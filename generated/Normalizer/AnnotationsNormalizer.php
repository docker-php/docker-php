<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class AnnotationsNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\Annotations') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\Annotations) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\Annotations();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Name')) {
            $object->setName($data->{'Name'});
        }
        if (property_exists($data, 'Labels')) {
            $value = $data->{'Labels'};
            if (is_object($data->{'Labels'})) {
                $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'Labels'} as $key => $value_1) {
                    $values[$key] = $value_1;
                }
                $value = $values;
            }
            if (is_null($data->{'Labels'})) {
                $value = $data->{'Labels'};
            }
            $object->setLabels($value);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        $value = $object->getLabels();
        if (is_object($object->getLabels())) {
            $values = new \stdClass();
            foreach ($object->getLabels() as $key => $value_1) {
                $values->{$key} = $value_1;
            }
            $value = $values;
        }
        if (is_null($object->getLabels())) {
            $value = $object->getLabels();
        }
        $data->{'Labels'} = $value;

        return $data;
    }
}
