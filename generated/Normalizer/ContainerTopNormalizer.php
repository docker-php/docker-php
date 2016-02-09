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
            $values_147 = [];
            foreach ($data->{'Titles'} as $value_148) {
                $values_147[] = $value_148;
            }
            $object->setTitles($values_147);
        }
        if (isset($data->{'Processes'})) {
            $values_149 = [];
            foreach ($data->{'Processes'} as $value_150) {
                $values_151 = [];
                foreach ($value_150 as $value_152) {
                    $values_151[] = $value_152;
                }
                $values_149[] = $values_151;
            }
            $object->setProcesses($values_149);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getTitles()) {
            $values_153 = [];
            foreach ($object->getTitles() as $value_154) {
                $values_153[] = $value_154;
            }
            $data->{'Titles'} = $values_153;
        }
        if (null !== $object->getProcesses()) {
            $values_155 = [];
            foreach ($object->getProcesses() as $value_156) {
                $values_157 = [];
                foreach ($value_156 as $value_158) {
                    $values_157[] = $value_158;
                }
                $values_155[] = $values_157;
            }
            $data->{'Processes'} = $values_155;
        }

        return $data;
    }
}
