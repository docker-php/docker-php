<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ContainerNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\Container') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\Container) {
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
        $object = new \Docker\API\Model\Container();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'AppArmorProfile'})) {
            $object->setAppArmorProfile($data->{'AppArmorProfile'});
        }
        if (isset($data->{'Args'})) {
            $values_103 = [];
            foreach ($data->{'Args'} as $value_104) {
                $values_103[] = $value_104;
            }
            $object->setArgs($values_103);
        }
        if (isset($data->{'Config'})) {
            $object->setConfig($this->serializer->deserialize($data->{'Config'}, 'Docker\\API\\Model\\ContainerConfig', 'raw', $context));
        }
        if (isset($data->{'Created'})) {
            $object->setCreated($data->{'Created'});
        }
        if (isset($data->{'Driver'})) {
            $object->setDriver($data->{'Driver'});
        }
        if (isset($data->{'ExecDriver'})) {
            $object->setExecDriver($data->{'ExecDriver'});
        }
        if (isset($data->{'ExecIDs'})) {
            $object->setExecIDs($data->{'ExecIDs'});
        }
        if (isset($data->{'HostConfig'})) {
            $object->setHostConfig($this->serializer->deserialize($data->{'HostConfig'}, 'Docker\\API\\Model\\HostConfig', 'raw', $context));
        }
        if (isset($data->{'HostnamePath'})) {
            $object->setHostnamePath($data->{'HostnamePath'});
        }
        if (isset($data->{'HostsPath'})) {
            $object->setHostsPath($data->{'HostsPath'});
        }
        if (isset($data->{'LogPath'})) {
            $object->setLogPath($data->{'LogPath'});
        }
        if (isset($data->{'Id'})) {
            $object->setId($data->{'Id'});
        }
        if (isset($data->{'Image'})) {
            $object->setImage($data->{'Image'});
        }
        if (isset($data->{'MountLabel'})) {
            $object->setMountLabel($data->{'MountLabel'});
        }
        if (isset($data->{'Name'})) {
            $object->setName($data->{'Name'});
        }
        if (isset($data->{'NetworkSettings'})) {
            $object->setNetworkSettings($this->serializer->deserialize($data->{'NetworkSettings'}, 'Docker\\API\\Model\\NetworkConfig', 'raw', $context));
        }
        if (isset($data->{'Path'})) {
            $object->setPath($data->{'Path'});
        }
        if (isset($data->{'ProcessLabel'})) {
            $object->setProcessLabel($data->{'ProcessLabel'});
        }
        if (isset($data->{'ResolvConfPath'})) {
            $object->setResolvConfPath($data->{'ResolvConfPath'});
        }
        if (isset($data->{'RestartCount'})) {
            $object->setRestartCount($data->{'RestartCount'});
        }
        if (isset($data->{'State'})) {
            $object->setState($this->serializer->deserialize($data->{'State'}, 'Docker\\API\\Model\\ContainerState', 'raw', $context));
        }
        if (isset($data->{'Mounts'})) {
            $values_105 = [];
            foreach ($data->{'Mounts'} as $value_106) {
                $values_105[] = $this->serializer->deserialize($value_106, 'Docker\\API\\Model\\Mount', 'raw', $context);
            }
            $object->setMounts($values_105);
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getAppArmorProfile()) {
            $data->{'AppArmorProfile'} = $object->getAppArmorProfile();
        }
        if (null !== $object->getArgs()) {
            $values_107 = [];
            foreach ($object->getArgs() as $value_108) {
                $values_107[] = $value_108;
            }
            $data->{'Args'} = $values_107;
        }
        if (null !== $object->getConfig()) {
            $data->{'Config'} = $this->serializer->serialize($object->getConfig(), 'raw', $context);
        }
        if (null !== $object->getCreated()) {
            $data->{'Created'} = $object->getCreated();
        }
        if (null !== $object->getDriver()) {
            $data->{'Driver'} = $object->getDriver();
        }
        if (null !== $object->getExecDriver()) {
            $data->{'ExecDriver'} = $object->getExecDriver();
        }
        if (null !== $object->getExecIDs()) {
            $data->{'ExecIDs'} = $object->getExecIDs();
        }
        if (null !== $object->getHostConfig()) {
            $data->{'HostConfig'} = $this->serializer->serialize($object->getHostConfig(), 'raw', $context);
        }
        if (null !== $object->getHostnamePath()) {
            $data->{'HostnamePath'} = $object->getHostnamePath();
        }
        if (null !== $object->getHostsPath()) {
            $data->{'HostsPath'} = $object->getHostsPath();
        }
        if (null !== $object->getLogPath()) {
            $data->{'LogPath'} = $object->getLogPath();
        }
        if (null !== $object->getId()) {
            $data->{'Id'} = $object->getId();
        }
        if (null !== $object->getImage()) {
            $data->{'Image'} = $object->getImage();
        }
        if (null !== $object->getMountLabel()) {
            $data->{'MountLabel'} = $object->getMountLabel();
        }
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getNetworkSettings()) {
            $data->{'NetworkSettings'} = $this->serializer->serialize($object->getNetworkSettings(), 'raw', $context);
        }
        if (null !== $object->getPath()) {
            $data->{'Path'} = $object->getPath();
        }
        if (null !== $object->getProcessLabel()) {
            $data->{'ProcessLabel'} = $object->getProcessLabel();
        }
        if (null !== $object->getResolvConfPath()) {
            $data->{'ResolvConfPath'} = $object->getResolvConfPath();
        }
        if (null !== $object->getRestartCount()) {
            $data->{'RestartCount'} = $object->getRestartCount();
        }
        if (null !== $object->getState()) {
            $data->{'State'} = $this->serializer->serialize($object->getState(), 'raw', $context);
        }
        if (null !== $object->getMounts()) {
            $values_109 = [];
            foreach ($object->getMounts() as $value_110) {
                $values_109[] = $this->serializer->serialize($value_110, 'raw', $context);
            }
            $data->{'Mounts'} = $values_109;
        }

        return $data;
    }
}
