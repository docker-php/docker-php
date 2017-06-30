<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class SwarmIPAMOptionsNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\SwarmIPAMOptions') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\SwarmIPAMOptions) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\SwarmIPAMOptions();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Driver')) {
            $object->setDriver($this->serializer->deserialize($data->{'Driver'}, 'Docker\\API\\Model\\Driver', 'raw', $context));
        }
        if (property_exists($data, 'Configs')) {
            $value = $data->{'Configs'};
            if (is_array($data->{'Configs'})) {
                $values = [];
                foreach ($data->{'Configs'} as $value_1) {
                    $values[] = $this->serializer->deserialize($value_1, 'Docker\\API\\Model\\IPAMConfig', 'raw', $context);
                }
                $value = $values;
            }
            if (is_null($data->{'Configs'})) {
                $value = $data->{'Configs'};
            }
            $object->setConfigs($value);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getDriver()) {
            $data->{'Driver'} = $this->serializer->serialize($object->getDriver(), 'raw', $context);
        }
        $value = $object->getConfigs();
        if (is_array($object->getConfigs())) {
            $values = [];
            foreach ($object->getConfigs() as $value_1) {
                $values[] = $this->serializer->serialize($value_1, 'raw', $context);
            }
            $value = $values;
        }
        if (is_null($object->getConfigs())) {
            $value = $object->getConfigs();
        }
        $data->{'Configs'} = $value;

        return $data;
    }
}
