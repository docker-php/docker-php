<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ImageItemNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ImageItem') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ImageItem) {
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
        $object = new \Docker\API\Model\ImageItem();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'RepoTags'})) {
            $values_123 = [];
            foreach ($data->{'RepoTags'} as $value_124) {
                $values_123[] = $value_124;
            }
            $object->setRepoTags($values_123);
        }
        if (isset($data->{'Id'})) {
            $object->setId($data->{'Id'});
        }
        if (isset($data->{'ParentId'})) {
            $object->setParentId($data->{'ParentId'});
        }
        if (isset($data->{'Created'})) {
            $object->setCreated($data->{'Created'});
        }
        if (isset($data->{'Size'})) {
            $object->setSize($data->{'Size'});
        }
        if (isset($data->{'VirtualSize'})) {
            $object->setVirtualSize($data->{'VirtualSize'});
        }
        if (isset($data->{'Labels'})) {
            $values_125 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Labels'} as $key_127 => $value_126) {
                $values_125[$key_127] = $value_126;
            }
            $object->setLabels($values_125);
        }
        if (isset($data->{'RepoDigests'})) {
            $values_128 = [];
            foreach ($data->{'RepoDigests'} as $value_129) {
                $values_128[] = $value_129;
            }
            $object->setRepoDigests($values_128);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getRepoTags()) {
            $values_130 = [];
            foreach ($object->getRepoTags() as $value_131) {
                $values_130[] = $value_131;
            }
            $data->{'RepoTags'} = $values_130;
        }
        if (null !== $object->getId()) {
            $data->{'Id'} = $object->getId();
        }
        if (null !== $object->getParentId()) {
            $data->{'ParentId'} = $object->getParentId();
        }
        if (null !== $object->getCreated()) {
            $data->{'Created'} = $object->getCreated();
        }
        if (null !== $object->getSize()) {
            $data->{'Size'} = $object->getSize();
        }
        if (null !== $object->getVirtualSize()) {
            $data->{'VirtualSize'} = $object->getVirtualSize();
        }
        if (null !== $object->getLabels()) {
            $values_132 = new \stdClass();
            foreach ($object->getLabels() as $key_134 => $value_133) {
                $values_132->{$key_134} = $value_133;
            }
            $data->{'Labels'} = $values_132;
        }
        if (null !== $object->getRepoDigests()) {
            $values_135 = [];
            foreach ($object->getRepoDigests() as $value_136) {
                $values_135[] = $value_136;
            }
            $data->{'RepoDigests'} = $values_135;
        }

        return $data;
    }
}
