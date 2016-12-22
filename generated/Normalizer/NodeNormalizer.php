<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class NodeNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\Node') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\Node) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\Node();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'ID')) {
            $object->setID($data->{'ID'});
        }
        if (property_exists($data, 'Version')) {
            $object->setVersion($this->serializer->deserialize($data->{'Version'}, 'Docker\\API\\Model\\NodeVersion', 'raw', $context));
        }
        if (property_exists($data, 'CreatedAt')) {
            $object->setCreatedAt(\DateTime::createFromFormat("Y-m-d\TH:i:sP", $data->{'CreatedAt'}));
        }
        if (property_exists($data, 'UpdatedAt')) {
            $object->setUpdatedAt(\DateTime::createFromFormat("Y-m-d\TH:i:sP", $data->{'UpdatedAt'}));
        }
        if (property_exists($data, 'Spec')) {
            $object->setSpec($this->serializer->deserialize($data->{'Spec'}, 'Docker\\API\\Model\\NodeSpec', 'raw', $context));
        }
        if (property_exists($data, 'Description')) {
            $object->setDescription($this->serializer->deserialize($data->{'Description'}, 'Docker\\API\\Model\\NodeDescription', 'raw', $context));
        }
        if (property_exists($data, 'Status')) {
            $object->setStatus($this->serializer->deserialize($data->{'Status'}, 'Docker\\API\\Model\\NodeStatus', 'raw', $context));
        }
        if (property_exists($data, 'ManagerStatus')) {
            $object->setManagerStatus($this->serializer->deserialize($data->{'ManagerStatus'}, 'Docker\\API\\Model\\NodeManagerStatus', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getID()) {
            $data->{'ID'} = $object->getID();
        }
        if (null !== $object->getVersion()) {
            $data->{'Version'} = $this->serializer->serialize($object->getVersion(), 'raw', $context);
        }
        if (null !== $object->getCreatedAt()) {
            $data->{'CreatedAt'} = $object->getCreatedAt()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getUpdatedAt()) {
            $data->{'UpdatedAt'} = $object->getUpdatedAt()->format("Y-m-d\TH:i:sP");
        }
        if (null !== $object->getSpec()) {
            $data->{'Spec'} = $this->serializer->serialize($object->getSpec(), 'raw', $context);
        }
        if (null !== $object->getDescription()) {
            $data->{'Description'} = $this->serializer->serialize($object->getDescription(), 'raw', $context);
        }
        if (null !== $object->getStatus()) {
            $data->{'Status'} = $this->serializer->serialize($object->getStatus(), 'raw', $context);
        }
        if (null !== $object->getManagerStatus()) {
            $data->{'ManagerStatus'} = $this->serializer->serialize($object->getManagerStatus(), 'raw', $context);
        }

        return $data;
    }
}
