<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ContainerSpecNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ContainerSpec') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ContainerSpec) {
            return true;
        }

        return false;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        if (isset($data->{'$ref'})) {
            return new Reference($data->{'$ref'}, $context['rootSchema'] ?: null);
        }
        $object = new \Docker\API\Model\ContainerSpec();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Image')) {
            $object->setImage($data->{'Image'});
        }
        if (property_exists($data, 'Labels')) {
            $value = $data->{'Labels'};
            if (is_object($data->{'Labels'})) {
                $values = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'Labels'} as $key => $value_1) {
                    $values[$key] = $value_1;
                }
                $value = $values;
            }
            if (is_null($data->{'Labels'})) {
                $value = $data->{'Labels'};
            }
            $object->setLabels($value);
        }
        if (property_exists($data, 'Command')) {
            $value_2 = $data->{'Command'};
            if (is_array($data->{'Command'})) {
                $values_1 = [];
                foreach ($data->{'Command'} as $value_3) {
                    $values_1[] = $value_3;
                }
                $value_2 = $values_1;
            }
            if (is_null($data->{'Command'})) {
                $value_2 = $data->{'Command'};
            }
            $object->setCommand($value_2);
        }
        if (property_exists($data, 'Args')) {
            $value_4 = $data->{'Args'};
            if (is_array($data->{'Args'})) {
                $values_2 = [];
                foreach ($data->{'Args'} as $value_5) {
                    $values_2[] = $value_5;
                }
                $value_4 = $values_2;
            }
            if (is_null($data->{'Args'})) {
                $value_4 = $data->{'Args'};
            }
            $object->setArgs($value_4);
        }
        if (property_exists($data, 'Env')) {
            $value_6 = $data->{'Env'};
            if (is_array($data->{'Env'})) {
                $values_3 = [];
                foreach ($data->{'Env'} as $value_7) {
                    $values_3[] = $value_7;
                }
                $value_6 = $values_3;
            }
            if (is_null($data->{'Env'})) {
                $value_6 = $data->{'Env'};
            }
            $object->setEnv($value_6);
        }
        if (property_exists($data, 'Dir')) {
            $object->setDir($data->{'Dir'});
        }
        if (property_exists($data, 'User')) {
            $object->setUser($data->{'User'});
        }
        if (property_exists($data, 'Mounts')) {
            $value_8 = $data->{'Mounts'};
            if (is_array($data->{'Mounts'})) {
                $values_4 = [];
                foreach ($data->{'Mounts'} as $value_9) {
                    $values_4[] = $this->serializer->deserialize($value_9, 'Docker\\API\\Model\\ContainerSpecMount', 'raw', $context);
                }
                $value_8 = $values_4;
            }
            if (is_null($data->{'Mounts'})) {
                $value_8 = $data->{'Mounts'};
            }
            $object->setMounts($value_8);
        }
        if (property_exists($data, 'StopGracePeriod')) {
            $object->setStopGracePeriod($data->{'StopGracePeriod'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getImage()) {
            $data->{'Image'} = $object->getImage();
        }
        $value = $object->getLabels();
        if (is_object($object->getLabels())) {
            $values = new \stdClass();
            foreach ($object->getLabels() as $key => $value_1) {
                $values->{$key} = $value_1;
            }
            $value = $values;
        }
        if (is_null($object->getLabels())) {
            $value = $object->getLabels();
        }
        $data->{'Labels'} = $value;
        $value_2          = $object->getCommand();
        if (is_array($object->getCommand())) {
            $values_1 = [];
            foreach ($object->getCommand() as $value_3) {
                $values_1[] = $value_3;
            }
            $value_2 = $values_1;
        }
        if (is_null($object->getCommand())) {
            $value_2 = $object->getCommand();
        }
        $data->{'Command'} = $value_2;
        $value_4           = $object->getArgs();
        if (is_array($object->getArgs())) {
            $values_2 = [];
            foreach ($object->getArgs() as $value_5) {
                $values_2[] = $value_5;
            }
            $value_4 = $values_2;
        }
        if (is_null($object->getArgs())) {
            $value_4 = $object->getArgs();
        }
        $data->{'Args'} = $value_4;
        $value_6        = $object->getEnv();
        if (is_array($object->getEnv())) {
            $values_3 = [];
            foreach ($object->getEnv() as $value_7) {
                $values_3[] = $value_7;
            }
            $value_6 = $values_3;
        }
        if (is_null($object->getEnv())) {
            $value_6 = $object->getEnv();
        }
        $data->{'Env'} = $value_6;
        if (null !== $object->getDir()) {
            $data->{'Dir'} = $object->getDir();
        }
        if (null !== $object->getUser()) {
            $data->{'User'} = $object->getUser();
        }
        $value_8 = $object->getMounts();
        if (is_array($object->getMounts())) {
            $values_4 = [];
            foreach ($object->getMounts() as $value_9) {
                $values_4[] = $this->serializer->serialize($value_9, 'raw', $context);
            }
            $value_8 = $values_4;
        }
        if (is_null($object->getMounts())) {
            $value_8 = $object->getMounts();
        }
        $data->{'Mounts'} = $value_8;
        if (null !== $object->getStopGracePeriod()) {
            $data->{'StopGracePeriod'} = $object->getStopGracePeriod();
        }

        return $data;
    }
}
