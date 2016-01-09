<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class PullInfoNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\PullInfo') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\PullInfo) {
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
        $object = new \Docker\API\Model\PullInfo();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'error'})) {
            $object->setError($data->{'error'});
        }
        if (isset($data->{'status'})) {
            $object->setStatus($data->{'status'});
        }
        if (isset($data->{'progress'})) {
            $object->setProgress($data->{'progress'});
        }
        if (isset($data->{'progressDetail'})) {
            $object->setProgressDetail($this->serializer->deserialize($data->{'progressDetail'}, 'Docker\\API\\Model\\ProgressDetail', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getError()) {
            $data->{'error'} = $object->getError();
        }
        if (null !== $object->getStatus()) {
            $data->{'status'} = $object->getStatus();
        }
        if (null !== $object->getProgress()) {
            $data->{'progress'} = $object->getProgress();
        }
        if (null !== $object->getProgressDetail()) {
            $data->{'progressDetail'} = $this->serializer->serialize($object->getProgressDetail(), 'raw', $context);
        }

        return $data;
    }
}
