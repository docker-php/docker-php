<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ContainerTopNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ContainerTop') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ContainerTop) {
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
        $object = new \Docker\API\Model\ContainerTop();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Titles'})) {
            $values_119 = [];
            foreach ($data->{'Titles'} as $value_120) {
                $values_119[] = $value_120;
            }
            $object->setTitles($values_119);
        }
        if (isset($data->{'Processes'})) {
            $values_121 = [];
            foreach ($data->{'Processes'} as $value_122) {
                $values_123 = [];
                foreach ($value_122 as $value_124) {
                    $values_123[] = $value_124;
                }
                $values_121[] = $values_123;
            }
            $object->setProcesses($values_121);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getTitles()) {
            $values_125 = [];
            foreach ($object->getTitles() as $value_126) {
                $values_125[] = $value_126;
            }
            $data->{'Titles'} = $values_125;
        }
        if (null !== $object->getProcesses()) {
            $values_127 = [];
            foreach ($object->getProcesses() as $value_128) {
                $values_129 = [];
                foreach ($value_128 as $value_130) {
                    $values_129[] = $value_130;
                }
                $values_127[] = $values_129;
            }
            $data->{'Processes'} = $values_127;
        }

        return $data;
    }
}
