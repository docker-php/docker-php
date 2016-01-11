<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class VersionNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\Version') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\Version) {
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
        $object = new \Docker\API\Model\Version();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Version'})) {
            $object->setVersion($data->{'Version'});
        }
        if (isset($data->{'Os'})) {
            $object->setOs($data->{'Os'});
        }
        if (isset($data->{'KernelVersion'})) {
            $object->setKernelVersion($data->{'KernelVersion'});
        }
        if (isset($data->{'GoVersion'})) {
            $object->setGoVersion($data->{'GoVersion'});
        }
        if (isset($data->{'GitCommit'})) {
            $object->setGitCommit($data->{'GitCommit'});
        }
        if (isset($data->{'Arch'})) {
            $object->setArch($data->{'Arch'});
        }
        if (isset($data->{'ApiVersion'})) {
            $object->setApiVersion($data->{'ApiVersion'});
        }
        if (isset($data->{'Experimental'})) {
            $object->setExperimental($data->{'Experimental'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getVersion()) {
            $data->{'Version'} = $object->getVersion();
        }
        if (null !== $object->getOs()) {
            $data->{'Os'} = $object->getOs();
        }
        if (null !== $object->getKernelVersion()) {
            $data->{'KernelVersion'} = $object->getKernelVersion();
        }
        if (null !== $object->getGoVersion()) {
            $data->{'GoVersion'} = $object->getGoVersion();
        }
        if (null !== $object->getGitCommit()) {
            $data->{'GitCommit'} = $object->getGitCommit();
        }
        if (null !== $object->getArch()) {
            $data->{'Arch'} = $object->getArch();
        }
        if (null !== $object->getApiVersion()) {
            $data->{'ApiVersion'} = $object->getApiVersion();
        }
        if (null !== $object->getExperimental()) {
            $data->{'Experimental'} = $object->getExperimental();
        }

        return $data;
    }
}
