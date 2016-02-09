<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class RegistryNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\Registry') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\Registry) {
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
        $object = new \Docker\API\Model\Registry();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Mirrors'})) {
            $values_215 = [];
            foreach ($data->{'Mirrors'} as $value_216) {
                $values_215[] = $value_216;
            }
            $object->setMirrors($values_215);
        }
        if (isset($data->{'Name'})) {
            $object->setName($data->{'Name'});
        }
        if (isset($data->{'Official'})) {
            $object->setOfficial($data->{'Official'});
        }
        if (isset($data->{'Secure'})) {
            $object->setSecure($data->{'Secure'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getMirrors()) {
            $values_217 = [];
            foreach ($object->getMirrors() as $value_218) {
                $values_217[] = $value_218;
            }
            $data->{'Mirrors'} = $values_217;
        }
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getOfficial()) {
            $data->{'Official'} = $object->getOfficial();
        }
        if (null !== $object->getSecure()) {
            $data->{'Secure'} = $object->getSecure();
        }

        return $data;
    }
}
