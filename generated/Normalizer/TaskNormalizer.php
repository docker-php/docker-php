<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class TaskNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\Task') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\Task) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\Task();
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
        if (property_exists($data, 'Name')) {
            $object->setName($data->{'Name'});
        }
        if (property_exists($data, 'Spec')) {
            $object->setSpec($this->serializer->deserialize($data->{'Spec'}, 'Docker\\API\\Model\\TaskSpec', 'raw', $context));
        }
        if (property_exists($data, 'ServiceID')) {
            $object->setServiceID($data->{'ServiceID'});
        }
        if (property_exists($data, 'Instance')) {
            $object->setInstance($data->{'Instance'});
        }
        if (property_exists($data, 'NodeID')) {
            $object->setNodeID($data->{'NodeID'});
        }
        if (property_exists($data, 'ServiceAnnotations')) {
            $object->setServiceAnnotations($this->serializer->deserialize($data->{'ServiceAnnotations'}, 'Docker\\API\\Model\\Annotations', 'raw', $context));
        }
        if (property_exists($data, 'Status')) {
            $object->setStatus($this->serializer->deserialize($data->{'Status'}, 'Docker\\API\\Model\\TaskStatus', 'raw', $context));
        }
        if (property_exists($data, 'DesiredState')) {
            $object->setDesiredState($data->{'DesiredState'});
        }
        if (property_exists($data, 'NetworksAttachments')) {
            $value = $data->{'NetworksAttachments'};
            if (is_array($data->{'NetworksAttachments'})) {
                $values = [];
                foreach ($data->{'NetworksAttachments'} as $value_1) {
                    $values[] = $this->serializer->deserialize($value_1, 'Docker\\API\\Model\\NetworkAttachment', 'raw', $context);
                }
                $value = $values;
            }
            if (is_null($data->{'NetworksAttachments'})) {
                $value = $data->{'NetworksAttachments'};
            }
            $object->setNetworksAttachments($value);
        }
        if (property_exists($data, 'Endpoint')) {
            $object->setEndpoint($this->serializer->deserialize($data->{'Endpoint'}, 'Docker\\API\\Model\\Endpoint', 'raw', $context));
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
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getSpec()) {
            $data->{'Spec'} = $this->serializer->serialize($object->getSpec(), 'raw', $context);
        }
        if (null !== $object->getServiceID()) {
            $data->{'ServiceID'} = $object->getServiceID();
        }
        if (null !== $object->getInstance()) {
            $data->{'Instance'} = $object->getInstance();
        }
        if (null !== $object->getNodeID()) {
            $data->{'NodeID'} = $object->getNodeID();
        }
        if (null !== $object->getServiceAnnotations()) {
            $data->{'ServiceAnnotations'} = $this->serializer->serialize($object->getServiceAnnotations(), 'raw', $context);
        }
        if (null !== $object->getStatus()) {
            $data->{'Status'} = $this->serializer->serialize($object->getStatus(), 'raw', $context);
        }
        if (null !== $object->getDesiredState()) {
            $data->{'DesiredState'} = $object->getDesiredState();
        }
        $value = $object->getNetworksAttachments();
        if (is_array($object->getNetworksAttachments())) {
            $values = [];
            foreach ($object->getNetworksAttachments() as $value_1) {
                $values[] = $this->serializer->serialize($value_1, 'raw', $context);
            }
            $value = $values;
        }
        if (is_null($object->getNetworksAttachments())) {
            $value = $object->getNetworksAttachments();
        }
        $data->{'NetworksAttachments'} = $value;
        if (null !== $object->getEndpoint()) {
            $data->{'Endpoint'} = $this->serializer->serialize($object->getEndpoint(), 'raw', $context);
        }

        return $data;
    }
}
