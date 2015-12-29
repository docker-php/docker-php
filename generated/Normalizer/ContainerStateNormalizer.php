<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ContainerStateNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ContainerState') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ContainerState) {
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
        $object = new \Docker\API\Model\ContainerState();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Error'})) {
            $object->setError($data->{'Error'});
        }
        if (isset($data->{'ExitCode'})) {
            $object->setExitCode($data->{'ExitCode'});
        }
        if (isset($data->{'FinishedAt'})) {
            $object->setFinishedAt($data->{'FinishedAt'});
        }
        if (isset($data->{'OOMKilled'})) {
            $object->setOOMKilled($data->{'OOMKilled'});
        }
        if (isset($data->{'Paused'})) {
            $object->setPaused($data->{'Paused'});
        }
        if (isset($data->{'Pid'})) {
            $object->setPid($data->{'Pid'});
        }
        if (isset($data->{'Restarting'})) {
            $object->setRestarting($data->{'Restarting'});
        }
        if (isset($data->{'Running'})) {
            $object->setRunning($data->{'Running'});
        }
        if (isset($data->{'StartedAt'})) {
            $object->setStartedAt($data->{'StartedAt'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getError()) {
            $data->{'Error'} = $object->getError();
        }
        if (null !== $object->getExitCode()) {
            $data->{'ExitCode'} = $object->getExitCode();
        }
        if (null !== $object->getFinishedAt()) {
            $data->{'FinishedAt'} = $object->getFinishedAt();
        }
        if (null !== $object->getOOMKilled()) {
            $data->{'OOMKilled'} = $object->getOOMKilled();
        }
        if (null !== $object->getPaused()) {
            $data->{'Paused'} = $object->getPaused();
        }
        if (null !== $object->getPid()) {
            $data->{'Pid'} = $object->getPid();
        }
        if (null !== $object->getRestarting()) {
            $data->{'Restarting'} = $object->getRestarting();
        }
        if (null !== $object->getRunning()) {
            $data->{'Running'} = $object->getRunning();
        }
        if (null !== $object->getStartedAt()) {
            $data->{'StartedAt'} = $object->getStartedAt();
        }

        return $data;
    }
}
