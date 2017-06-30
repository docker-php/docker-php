<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NetworkAttachmentNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\NetworkAttachment') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\NetworkAttachment) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\NetworkAttachment();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Network')) {
            $object->setNetwork($this->serializer->deserialize($data->{'Network'}, 'Docker\\API\\Model\\SwarmNetwork', 'raw', $context));
        }
        if (property_exists($data, 'Addresses')) {
            $value = $data->{'Addresses'};
            if (is_array($data->{'Addresses'})) {
                $values = [];
                foreach ($data->{'Addresses'} as $value_1) {
                    $values[] = $value_1;
                }
                $value = $values;
            }
            if (is_null($data->{'Addresses'})) {
                $value = $data->{'Addresses'};
            }
            $object->setAddresses($value);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getNetwork()) {
            $data->{'Network'} = $this->serializer->serialize($object->getNetwork(), 'raw', $context);
        }
        $value = $object->getAddresses();
        if (is_array($object->getAddresses())) {
            $values = [];
            foreach ($object->getAddresses() as $value_1) {
                $values[] = $value_1;
            }
            $value = $values;
        }
        if (is_null($object->getAddresses())) {
            $value = $object->getAddresses();
        }
        $data->{'Addresses'} = $value;

        return $data;
    }
}
