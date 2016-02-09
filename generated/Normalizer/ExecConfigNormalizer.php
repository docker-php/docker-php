<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ExecConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ExecConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ExecConfig) {
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
        $object = new \Docker\API\Model\ExecConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'AttachStdin'})) {
            $object->setAttachStdin($data->{'AttachStdin'});
        }
        if (isset($data->{'AttachStdout'})) {
            $object->setAttachStdout($data->{'AttachStdout'});
        }
        if (isset($data->{'AttachStderr'})) {
            $object->setAttachStderr($data->{'AttachStderr'});
        }
        if (isset($data->{'Tty'})) {
            $object->setTty($data->{'Tty'});
        }
        if (isset($data->{'Cmd'})) {
            $values_187 = [];
            foreach ($data->{'Cmd'} as $value_188) {
                $values_187[] = $value_188;
            }
            $object->setCmd($values_187);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getAttachStdin()) {
            $data->{'AttachStdin'} = $object->getAttachStdin();
        }
        if (null !== $object->getAttachStdout()) {
            $data->{'AttachStdout'} = $object->getAttachStdout();
        }
        if (null !== $object->getAttachStderr()) {
            $data->{'AttachStderr'} = $object->getAttachStderr();
        }
        if (null !== $object->getTty()) {
            $data->{'Tty'} = $object->getTty();
        }
        if (null !== $object->getCmd()) {
            $values_189 = [];
            foreach ($object->getCmd() as $value_190) {
                $values_189[] = $value_190;
            }
            $data->{'Cmd'} = $values_189;
        }

        return $data;
    }
}
