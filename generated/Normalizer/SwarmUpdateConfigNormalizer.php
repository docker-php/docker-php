<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class SwarmUpdateConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\SwarmUpdateConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\SwarmUpdateConfig) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\SwarmUpdateConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Name')) {
            $object->setName($data->{'Name'});
        }
        if (property_exists($data, 'Orchestration')) {
            $object->setOrchestration($this->serializer->deserialize($data->{'Orchestration'}, 'Docker\\API\\Model\\SwarmConfigSpecOrchestration', 'raw', $context));
        }
        if (property_exists($data, 'Raft')) {
            $object->setRaft($this->serializer->deserialize($data->{'Raft'}, 'Docker\\API\\Model\\SwarmConfigSpecRaft', 'raw', $context));
        }
        if (property_exists($data, 'Dispatcher')) {
            $object->setDispatcher($this->serializer->deserialize($data->{'Dispatcher'}, 'Docker\\API\\Model\\SwarmConfigSpecDispatcher', 'raw', $context));
        }
        if (property_exists($data, 'CAConfig')) {
            $object->setCAConfig($this->serializer->deserialize($data->{'CAConfig'}, 'Docker\\API\\Model\\SwarmConfigSpecCAConfig', 'raw', $context));
        }
        if (property_exists($data, 'JoinTokens')) {
            $object->setJoinTokens($this->serializer->deserialize($data->{'JoinTokens'}, 'Docker\\API\\Model\\SwarmJoinTokens', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getOrchestration()) {
            $data->{'Orchestration'} = $this->serializer->serialize($object->getOrchestration(), 'raw', $context);
        }
        if (null !== $object->getRaft()) {
            $data->{'Raft'} = $this->serializer->serialize($object->getRaft(), 'raw', $context);
        }
        if (null !== $object->getDispatcher()) {
            $data->{'Dispatcher'} = $this->serializer->serialize($object->getDispatcher(), 'raw', $context);
        }
        if (null !== $object->getCAConfig()) {
            $data->{'CAConfig'} = $this->serializer->serialize($object->getCAConfig(), 'raw', $context);
        }
        if (null !== $object->getJoinTokens()) {
            $data->{'JoinTokens'} = $this->serializer->serialize($object->getJoinTokens(), 'raw', $context);
        }

        return $data;
    }
}
