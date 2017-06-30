<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class UpdateConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\UpdateConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\UpdateConfig) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\UpdateConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Parallelism')) {
            $object->setParallelism($data->{'Parallelism'});
        }
        if (property_exists($data, 'Delay')) {
            $object->setDelay($data->{'Delay'});
        }
        if (property_exists($data, 'FailureAction')) {
            $object->setFailureAction($data->{'FailureAction'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getParallelism()) {
            $data->{'Parallelism'} = $object->getParallelism();
        }
        if (null !== $object->getDelay()) {
            $data->{'Delay'} = $object->getDelay();
        }
        if (null !== $object->getFailureAction()) {
            $data->{'FailureAction'} = $object->getFailureAction();
        }

        return $data;
    }
}
