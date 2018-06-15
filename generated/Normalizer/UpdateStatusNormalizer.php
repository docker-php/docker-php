<?php

namespace Docker\API\Normalizer;

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

        $dataCreatedAtString = $data->{'StartedAt'};
        $dataCreatedAtString = explode(".", $dataCreatedAtString)[0]."UTC";

        $dataUpdatedAtString = $data->{'CompletedAt'};
        $dataUpdatedAtString = explode(".", $dataUpdatedAtString)[0]."UTC";


        $object = new \Docker\API\Model\UpdateStatus();
        if (property_exists($data, 'State')) {
            $object->setState($data->{'State'});
        }
        if (property_exists($data, 'StartedAt')) {
            $object->setStartedAt(\DateTime::createFromFormat("Y-m-d\TH:i:sP", $dataCreatedAtString));
        }
        if (property_exists($data, 'CompletedAt')) {
            $object->setCompletedAt(\DateTime::createFromFormat("Y-m-d\TH:i:sP", $dataUpdatedAtString));
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