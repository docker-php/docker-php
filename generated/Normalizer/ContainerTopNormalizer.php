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
            $values_111 = [];
            foreach ($data->{'Titles'} as $value_112) {
                $values_111[] = $value_112;
            }
            $object->setTitles($values_111);
        }
        if (isset($data->{'Processes'})) {
            $values_113 = [];
            foreach ($data->{'Processes'} as $value_114) {
                $values_115 = [];
                foreach ($value_114 as $value_116) {
                    $values_115[] = $value_116;
                }
                $values_113[] = $values_115;
            }
            $object->setProcesses($values_113);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getTitles()) {
            $values_117 = [];
            foreach ($object->getTitles() as $value_118) {
                $values_117[] = $value_118;
            }
            $data->{'Titles'} = $values_117;
        }
        if (null !== $object->getProcesses()) {
            $values_119 = [];
            foreach ($object->getProcesses() as $value_120) {
                $values_121 = [];
                foreach ($value_120 as $value_122) {
                    $values_121[] = $value_122;
                }
                $values_119[] = $values_121;
            }
            $data->{'Processes'} = $values_119;
        }

        return $data;
    }
}
