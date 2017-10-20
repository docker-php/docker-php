<?php

namespace Docker\API\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class TaskSpecRestartPolicyNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\TaskSpecRestartPolicy') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\TaskSpecRestartPolicy) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $object = new \Docker\API\Model\TaskSpecRestartPolicy();
        if (property_exists($data, 'Condition')) {
            $object->setCondition($data->{'Condition'});
        }
        if (property_exists($data, 'Delay')) {
            $object->setDelay($data->{'Delay'});
        }
        if (property_exists($data, 'MaxAttempts')) {
            $object->setMaxAttempts($data->{'MaxAttempts'});
        }
        if (property_exists($data, 'Window')) {
            $object->setWindow($data->{'Window'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getCondition()) {
            $data->{'Condition'} = $object->getCondition();
        }
        if (null !== $object->getDelay()) {
            $data->{'Delay'} = $object->getDelay();
        }
        if (null !== $object->getMaxAttempts()) {
            $data->{'MaxAttempts'} = $object->getMaxAttempts();
        }
        if (null !== $object->getWindow()) {
            $data->{'Window'} = $object->getWindow();
        }

        return $data;
    }
}
