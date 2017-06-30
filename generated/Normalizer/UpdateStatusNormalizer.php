<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class UpdateStatusNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\UpdateStatus') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\UpdateStatus) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\UpdateStatus();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'State')) {
            $object->setState($data->{'State'});
        }
        if (property_exists($data, 'StartedAt')) {
            $object->setStartedAt(\DateTime::createFromFormat("Y-m-d\TH:i:sP", $data->{'StartedAt'}));
        }
        if (property_exists($data, 'CompletedAt')) {
            $object->setCompletedAt(\DateTime::createFromFormat("Y-m-d\TH:i:sP", $data->{'CompletedAt'}));
        }
        if (property_exists($data, 'Message')) {
            $object->setMessage($data->{'Message'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getState()) {
            $data->{'State'} = $object->getState();
        }
        if (null !== $object->getStartedAt()) {
            $data->{'StartedAt'} = $object->getStartedAt()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getCompletedAt()) {
            $data->{'CompletedAt'} = $object->getCompletedAt()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getMessage()) {
            $data->{'Message'} = $object->getMessage();
        }

        return $data;
    }
}
