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
            $values_159 = [];
            foreach ($data->{'RepoTags'} as $value_160) {
                $values_159[] = $value_160;
            }
            $object->setRepoTags($values_159);
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
            $values_161 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Labels'} as $key_163 => $value_162) {
                $values_161[$key_163] = $value_162;
            }
            $object->setLabels($values_161);
        }
        if (isset($data->{'RepoDigests'})) {
            $values_164 = [];
            foreach ($data->{'RepoDigests'} as $value_165) {
                $values_164[] = $value_165;
            }
            $object->setRepoDigests($values_164);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getRepoTags()) {
            $values_166 = [];
            foreach ($object->getRepoTags() as $value_167) {
                $values_166[] = $value_167;
            }
            $data->{'RepoTags'} = $values_166;
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
            $values_168 = new \stdClass();
            foreach ($object->getLabels() as $key_170 => $value_169) {
                $values_168->{$key_170} = $value_169;
            }
            $data->{'Labels'} = $values_168;
        }
        if (null !== $object->getRepoDigests()) {
            $values_171 = [];
            foreach ($object->getRepoDigests() as $value_172) {
                $values_171[] = $value_172;
            }
            $data->{'RepoDigests'} = $values_171;
        }

        return $data;
    }
}
