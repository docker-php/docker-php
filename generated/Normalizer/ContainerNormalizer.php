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
            $values_111 = [];
            foreach ($data->{'Args'} as $value_112) {
                $values_111[] = $value_112;
            }
            $object->setArgs($values_111);
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
            $values_113 = [];
            foreach ($data->{'Mounts'} as $value_114) {
                $values_113[] = $this->serializer->deserialize($value_114, 'Docker\\API\\Model\\Mount', 'raw', $context);
            }
            $object->setMounts($values_113);
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
            $values_115 = [];
            foreach ($object->getArgs() as $value_116) {
                $values_115[] = $value_116;
            }
            $data->{'Args'} = $values_115;
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
            $values_117 = [];
            foreach ($object->getMounts() as $value_118) {
                $values_117[] = $this->serializer->serialize($value_118, 'raw', $context);
            }
            $data->{'Mounts'} = $values_117;
        }

        return $data;
    }
}
