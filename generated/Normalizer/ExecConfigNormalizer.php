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
        if (property_exists($data, 'AttachStdin')) {
            $object->setAttachStdin($data->{'AttachStdin'});
        }
        if (property_exists($data, 'AttachStdout')) {
            $object->setAttachStdout($data->{'AttachStdout'});
        }
        if (property_exists($data, 'AttachStderr')) {
            $object->setAttachStderr($data->{'AttachStderr'});
        }
        if (property_exists($data, 'Tty')) {
            $object->setTty($data->{'Tty'});
        }
        if (property_exists($data, 'Cmd')) {
            $values = [];
            foreach ($data->{'Cmd'} as $value) {
                $values[] = $value;
            }
            $object->setCmd($values);
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
            $values = [];
            foreach ($object->getCmd() as $value) {
                $values[] = $value;
            }
            $data->{'Cmd'} = $values;
        }

        return $data;
    }
}
