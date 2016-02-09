<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ProcessConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ProcessConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ProcessConfig) {
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
        $object = new \Docker\API\Model\ProcessConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'privileged'})) {
            $object->setPrivileged($data->{'privileged'});
        }
        if (isset($data->{'user'})) {
            $object->setUser($data->{'user'});
        }
        if (isset($data->{'tty'})) {
            $object->setTty($data->{'tty'});
        }
        if (isset($data->{'entrypoint'})) {
            $object->setEntrypoint($data->{'entrypoint'});
        }
        if (isset($data->{'arguments'})) {
            $values_191 = [];
            foreach ($data->{'arguments'} as $value_192) {
                $values_191[] = $value_192;
            }
            $object->setArguments($values_191);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getPrivileged()) {
            $data->{'privileged'} = $object->getPrivileged();
        }
        if (null !== $object->getUser()) {
            $data->{'user'} = $object->getUser();
        }
        if (null !== $object->getTty()) {
            $data->{'tty'} = $object->getTty();
        }
        if (null !== $object->getEntrypoint()) {
            $data->{'entrypoint'} = $object->getEntrypoint();
        }
        if (null !== $object->getArguments()) {
            $values_193 = [];
            foreach ($object->getArguments() as $value_194) {
                $values_193[] = $value_194;
            }
            $data->{'arguments'} = $values_193;
        }

        return $data;
    }
}
