<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ServiceSpecNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ServiceSpec') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ServiceSpec) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\ServiceSpec();
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
        if (property_exists($data, 'TaskTemplate')) {
            $object->setTaskTemplate($this->serializer->deserialize($data->{'TaskTemplate'}, 'Docker\\API\\Model\\TaskSpec', 'raw', $context));
        }
        if (property_exists($data, 'Mode')) {
            $object->setMode($this->serializer->deserialize($data->{'Mode'}, 'Docker\\API\\Model\\ServiceSpecMode', 'raw', $context));
        }
        if (property_exists($data, 'UpdateConfig')) {
            $object->setUpdateConfig($this->serializer->deserialize($data->{'UpdateConfig'}, 'Docker\\API\\Model\\UpdateConfig', 'raw', $context));
        }
        if (property_exists($data, 'Networks')) {
            $value_2 = $data->{'Networks'};
            if (is_array($data->{'Networks'})) {
                $values_1 = [];
                foreach ($data->{'Networks'} as $value_3) {
                    $values_1[] = $this->serializer->deserialize($value_3, 'Docker\\API\\Model\\NetworkAttachmentConfig', 'raw', $context);
                }
                $value_2 = $values_1;
            }
            if (is_null($data->{'Networks'})) {
                $value_2 = $data->{'Networks'};
            }
            $object->setNetworks($value_2);
        }
        if (property_exists($data, 'EndpointSpec')) {
            $object->setEndpointSpec($data->{'EndpointSpec'});
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
        if (null !== $object->getTaskTemplate()) {
            $data->{'TaskTemplate'} = $this->serializer->serialize($object->getTaskTemplate(), 'raw', $context);
        }
        if (null !== $object->getMode()) {
            $data->{'Mode'} = $this->serializer->serialize($object->getMode(), 'raw', $context);
        }
        if (null !== $object->getUpdateConfig()) {
            $data->{'UpdateConfig'} = $this->serializer->serialize($object->getUpdateConfig(), 'raw', $context);
        }
        $value_2 = $object->getNetworks();
        if (is_array($object->getNetworks())) {
            $values_1 = [];
            foreach ($object->getNetworks() as $value_3) {
                $values_1[] = $this->serializer->serialize($value_3, 'raw', $context);
            }
            $value_2 = $values_1;
        }
        if (is_null($object->getNetworks())) {
            $value_2 = $object->getNetworks();
        }
        $data->{'Networks'} = $value_2;
        if (null !== $object->getEndpointSpec()) {
            $data->{'EndpointSpec'} = $object->getEndpointSpec();
        }

        return $data;
    }
}
