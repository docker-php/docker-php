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
            $values_131 = [];
            foreach ($data->{'RepoTags'} as $value_132) {
                $values_131[] = $value_132;
            }
            $object->setRepoTags($values_131);
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
            $values_133 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Labels'} as $key_135 => $value_134) {
                $values_133[$key_135] = $value_134;
            }
            $object->setLabels($values_133);
        }
        if (isset($data->{'RepoDigests'})) {
            $values_136 = [];
            foreach ($data->{'RepoDigests'} as $value_137) {
                $values_136[] = $value_137;
            }
            $object->setRepoDigests($values_136);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getRepoTags()) {
            $values_138 = [];
            foreach ($object->getRepoTags() as $value_139) {
                $values_138[] = $value_139;
            }
            $data->{'RepoTags'} = $values_138;
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
            $values_140 = new \stdClass();
            foreach ($object->getLabels() as $key_142 => $value_141) {
                $values_140->{$key_142} = $value_141;
            }
            $data->{'Labels'} = $values_140;
        }
        if (null !== $object->getRepoDigests()) {
            $values_143 = [];
            foreach ($object->getRepoDigests() as $value_144) {
                $values_143[] = $value_144;
            }
            $data->{'RepoDigests'} = $values_143;
        }

        return $data;
    }
}
