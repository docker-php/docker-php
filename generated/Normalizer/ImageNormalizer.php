<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ImageNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\Image') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\Image) {
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
        $object = new \Docker\API\Model\Image();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Id'})) {
            $object->setId($data->{'Id'});
        }
        if (isset($data->{'Container'})) {
            $object->setContainer($data->{'Container'});
        }
        if (isset($data->{'Comment'})) {
            $object->setComment($data->{'Comment'});
        }
        if (isset($data->{'Os'})) {
            $object->setOs($data->{'Os'});
        }
        if (isset($data->{'Architecture'})) {
            $object->setArchitecture($data->{'Architecture'});
        }
        if (isset($data->{'Parent'})) {
            $object->setParent($data->{'Parent'});
        }
        if (isset($data->{'ContainerConfig'})) {
            $object->setContainerConfig($this->serializer->deserialize($data->{'ContainerConfig'}, 'Docker\\API\\Model\\ContainerConfig', 'raw', $context));
        }
        if (isset($data->{'DockerVersion'})) {
            $object->setDockerVersion($data->{'DockerVersion'});
        }
        if (isset($data->{'VirtualSize'})) {
            $object->setVirtualSize($data->{'VirtualSize'});
        }
        if (isset($data->{'Size'})) {
            $object->setSize($data->{'Size'});
        }
        if (isset($data->{'Author'})) {
            $object->setAuthor($data->{'Author'});
        }
        if (isset($data->{'Created'})) {
            $object->setCreated($data->{'Created'});
        }
        if (isset($data->{'GraphDriver'})) {
            $object->setGraphDriver($this->serializer->deserialize($data->{'GraphDriver'}, 'Docker\\API\\Model\\GraphDriver', 'raw', $context));
        }
        if (isset($data->{'RepoDigests'})) {
            $values_173 = [];
            foreach ($data->{'RepoDigests'} as $value_174) {
                $values_173[] = $value_174;
            }
            $object->setRepoDigests($values_173);
        }
        if (isset($data->{'RepoTags'})) {
            $values_175 = [];
            foreach ($data->{'RepoTags'} as $value_176) {
                $values_175[] = $value_176;
            }
            $object->setRepoTags($values_175);
        }
        if (isset($data->{'Config'})) {
            $object->setConfig($this->serializer->deserialize($data->{'Config'}, 'Docker\\API\\Model\\ContainerConfig', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getId()) {
            $data->{'Id'} = $object->getId();
        }
        if (null !== $object->getContainer()) {
            $data->{'Container'} = $object->getContainer();
        }
        if (null !== $object->getComment()) {
            $data->{'Comment'} = $object->getComment();
        }
        if (null !== $object->getOs()) {
            $data->{'Os'} = $object->getOs();
        }
        if (null !== $object->getArchitecture()) {
            $data->{'Architecture'} = $object->getArchitecture();
        }
        if (null !== $object->getParent()) {
            $data->{'Parent'} = $object->getParent();
        }
        if (null !== $object->getContainerConfig()) {
            $data->{'ContainerConfig'} = $this->serializer->serialize($object->getContainerConfig(), 'raw', $context);
        }
        if (null !== $object->getDockerVersion()) {
            $data->{'DockerVersion'} = $object->getDockerVersion();
        }
        if (null !== $object->getVirtualSize()) {
            $data->{'VirtualSize'} = $object->getVirtualSize();
        }
        if (null !== $object->getSize()) {
            $data->{'Size'} = $object->getSize();
        }
        if (null !== $object->getAuthor()) {
            $data->{'Author'} = $object->getAuthor();
        }
        if (null !== $object->getCreated()) {
            $data->{'Created'} = $object->getCreated();
        }
        if (null !== $object->getGraphDriver()) {
            $data->{'GraphDriver'} = $this->serializer->serialize($object->getGraphDriver(), 'raw', $context);
        }
        if (null !== $object->getRepoDigests()) {
            $values_177 = [];
            foreach ($object->getRepoDigests() as $value_178) {
                $values_177[] = $value_178;
            }
            $data->{'RepoDigests'} = $values_177;
        }
        if (null !== $object->getRepoTags()) {
            $values_179 = [];
            foreach ($object->getRepoTags() as $value_180) {
                $values_179[] = $value_180;
            }
            $data->{'RepoTags'} = $values_179;
        }
        if (null !== $object->getConfig()) {
            $data->{'Config'} = $this->serializer->serialize($object->getConfig(), 'raw', $context);
        }

        return $data;
    }
}
