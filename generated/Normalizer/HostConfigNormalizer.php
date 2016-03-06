<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class HostConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\HostConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\HostConfig) {
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
        $object = new \Docker\API\Model\HostConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (property_exists($data, 'Binds')) {
            $values = [];
            foreach ($data->{'Binds'} as $value) {
                $values[] = $value;
            }
            $object->setBinds($values);
        }
        if (property_exists($data, 'Links')) {
            $values_1 = [];
            foreach ($data->{'Links'} as $value_1) {
                $values_1[] = $value_1;
            }
            $object->setLinks($values_1);
        }
        if (property_exists($data, 'LxcConf')) {
            $values_2 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'LxcConf'} as $key => $value_2) {
                $values_2[$key] = $value_2;
            }
            $object->setLxcConf($values_2);
        }
        if (property_exists($data, 'Memory')) {
            $object->setMemory($data->{'Memory'});
        }
        if (property_exists($data, 'MemorySwap')) {
            $object->setMemorySwap($data->{'MemorySwap'});
        }
        if (property_exists($data, 'CpuShares')) {
            $object->setCpuShares($data->{'CpuShares'});
        }
        if (property_exists($data, 'CpuPeriod')) {
            $object->setCpuPeriod($data->{'CpuPeriod'});
        }
        if (property_exists($data, 'CpusetCpus')) {
            $object->setCpusetCpus($data->{'CpusetCpus'});
        }
        if (property_exists($data, 'CpusetMems')) {
            $object->setCpusetMems($data->{'CpusetMems'});
        }
        if (property_exists($data, 'BlkioWeight')) {
            $object->setBlkioWeight($data->{'BlkioWeight'});
        }
        if (property_exists($data, 'BlkioWeightDevice')) {
            $values_3 = [];
            foreach ($data->{'BlkioWeightDevice'} as $value_3) {
                $values_3[] = $this->serializer->deserialize($value_3, 'Docker\\API\\Model\\DeviceWeight', 'raw', $context);
            }
            $object->setBlkioWeightDevice($values_3);
        }
        if (property_exists($data, 'BlkioDeviceReadBps')) {
            $values_4 = [];
            foreach ($data->{'BlkioDeviceReadBps'} as $value_4) {
                $values_4[] = $this->serializer->deserialize($value_4, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
            }
            $object->setBlkioDeviceReadBps($values_4);
        }
        if (property_exists($data, 'BlkioDeviceReadIOps')) {
            $values_5 = [];
            foreach ($data->{'BlkioDeviceReadIOps'} as $value_5) {
                $values_5[] = $this->serializer->deserialize($value_5, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
            }
            $object->setBlkioDeviceReadIOps($values_5);
        }
        if (property_exists($data, 'BlkioDeviceWriteBps')) {
            $values_6 = [];
            foreach ($data->{'BlkioDeviceWriteBps'} as $value_6) {
                $values_6[] = $this->serializer->deserialize($value_6, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
            }
            $object->setBlkioDeviceWriteBps($values_6);
        }
        if (property_exists($data, 'BlkioDeviceWriteIOps')) {
            $values_7 = [];
            foreach ($data->{'BlkioDeviceWriteIOps'} as $value_7) {
                $values_7[] = $this->serializer->deserialize($value_7, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
            }
            $object->setBlkioDeviceWriteIOps($values_7);
        }
        if (property_exists($data, 'MemorySwappiness')) {
            $object->setMemorySwappiness($data->{'MemorySwappiness'});
        }
        if (property_exists($data, 'OomKillDisable')) {
            $object->setOomKillDisable($data->{'OomKillDisable'});
        }
        if (property_exists($data, 'PortBindings')) {
            $values_8 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'PortBindings'} as $key_1 => $value_8) {
                $values_8[$key_1] = $this->serializer->deserialize($value_8, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
            }
            $object->setPortBindings($values_8);
        }
        if (property_exists($data, 'PublishAllPorts')) {
            $object->setPublishAllPorts($data->{'PublishAllPorts'});
        }
        if (property_exists($data, 'Privileged')) {
            $object->setPrivileged($data->{'Privileged'});
        }
        if (property_exists($data, 'ReadonlyRootfs')) {
            $object->setReadonlyRootfs($data->{'ReadonlyRootfs'});
        }
        if (property_exists($data, 'Dns')) {
            $values_9 = [];
            foreach ($data->{'Dns'} as $value_9) {
                $values_9[] = $value_9;
            }
            $object->setDns($values_9);
        }
        if (property_exists($data, 'DnsSearch')) {
            $values_10 = [];
            foreach ($data->{'DnsSearch'} as $value_10) {
                $values_10[] = $value_10;
            }
            $object->setDnsSearch($values_10);
        }
        if (property_exists($data, 'ExtraHosts')) {
            $values_11 = [];
            foreach ($data->{'ExtraHosts'} as $value_11) {
                $values_11[] = $value_11;
            }
            $object->setExtraHosts($values_11);
        }
        if (property_exists($data, 'VolumesFrom')) {
            $values_12 = [];
            foreach ($data->{'VolumesFrom'} as $value_12) {
                $values_12[] = $value_12;
            }
            $object->setVolumesFrom($values_12);
        }
        if (property_exists($data, 'CapAdd')) {
            $values_13 = [];
            foreach ($data->{'CapAdd'} as $value_13) {
                $values_13[] = $value_13;
            }
            $object->setCapAdd($values_13);
        }
        if (property_exists($data, 'CapDrop')) {
            $values_14 = [];
            foreach ($data->{'CapDrop'} as $value_14) {
                $values_14[] = $value_14;
            }
            $object->setCapDrop($values_14);
        }
        if (property_exists($data, 'RestartPolicy')) {
            $object->setRestartPolicy($this->serializer->deserialize($data->{'RestartPolicy'}, 'Docker\\API\\Model\\RestartPolicy', 'raw', $context));
        }
        if (property_exists($data, 'NetworkMode')) {
            $object->setNetworkMode($data->{'NetworkMode'});
        }
        if (property_exists($data, 'Devices')) {
            $values_15 = [];
            foreach ($data->{'Devices'} as $value_15) {
                $values_15[] = $this->serializer->deserialize($value_15, 'Docker\\API\\Model\\Device', 'raw', $context);
            }
            $object->setDevices($values_15);
        }
        if (property_exists($data, 'Ulimits')) {
            $values_16 = [];
            foreach ($data->{'Ulimits'} as $value_16) {
                $values_16[] = $this->serializer->deserialize($value_16, 'Docker\\API\\Model\\Ulimit', 'raw', $context);
            }
            $object->setUlimits($values_16);
        }
        if (property_exists($data, 'SecurityOpt')) {
            $values_17 = [];
            foreach ($data->{'SecurityOpt'} as $value_17) {
                $values_17[] = $value_17;
            }
            $object->setSecurityOpt($values_17);
        }
        if (property_exists($data, 'LogConfig')) {
            $object->setLogConfig($this->serializer->deserialize($data->{'LogConfig'}, 'Docker\\API\\Model\\LogConfig', 'raw', $context));
        }
        if (property_exists($data, 'CgroupParent')) {
            $object->setCgroupParent($data->{'CgroupParent'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getBinds()) {
            $values = [];
            foreach ($object->getBinds() as $value) {
                $values[] = $value;
            }
            $data->{'Binds'} = $values;
        }
        if (null !== $object->getLinks()) {
            $values_1 = [];
            foreach ($object->getLinks() as $value_1) {
                $values_1[] = $value_1;
            }
            $data->{'Links'} = $values_1;
        }
        if (null !== $object->getLxcConf()) {
            $values_2 = new \stdClass();
            foreach ($object->getLxcConf() as $key => $value_2) {
                $values_2->{$key} = $value_2;
            }
            $data->{'LxcConf'} = $values_2;
        }
        if (null !== $object->getMemory()) {
            $data->{'Memory'} = $object->getMemory();
        }
        if (null !== $object->getMemorySwap()) {
            $data->{'MemorySwap'} = $object->getMemorySwap();
        }
        if (null !== $object->getCpuShares()) {
            $data->{'CpuShares'} = $object->getCpuShares();
        }
        if (null !== $object->getCpuPeriod()) {
            $data->{'CpuPeriod'} = $object->getCpuPeriod();
        }
        if (null !== $object->getCpusetCpus()) {
            $data->{'CpusetCpus'} = $object->getCpusetCpus();
        }
        if (null !== $object->getCpusetMems()) {
            $data->{'CpusetMems'} = $object->getCpusetMems();
        }
        if (null !== $object->getBlkioWeight()) {
            $data->{'BlkioWeight'} = $object->getBlkioWeight();
        }
        if (null !== $object->getBlkioWeightDevice()) {
            $values_3 = [];
            foreach ($object->getBlkioWeightDevice() as $value_3) {
                $values_3[] = $this->serializer->serialize($value_3, 'raw', $context);
            }
            $data->{'BlkioWeightDevice'} = $values_3;
        }
        if (null !== $object->getBlkioDeviceReadBps()) {
            $values_4 = [];
            foreach ($object->getBlkioDeviceReadBps() as $value_4) {
                $values_4[] = $this->serializer->serialize($value_4, 'raw', $context);
            }
            $data->{'BlkioDeviceReadBps'} = $values_4;
        }
        if (null !== $object->getBlkioDeviceReadIOps()) {
            $values_5 = [];
            foreach ($object->getBlkioDeviceReadIOps() as $value_5) {
                $values_5[] = $this->serializer->serialize($value_5, 'raw', $context);
            }
            $data->{'BlkioDeviceReadIOps'} = $values_5;
        }
        if (null !== $object->getBlkioDeviceWriteBps()) {
            $values_6 = [];
            foreach ($object->getBlkioDeviceWriteBps() as $value_6) {
                $values_6[] = $this->serializer->serialize($value_6, 'raw', $context);
            }
            $data->{'BlkioDeviceWriteBps'} = $values_6;
        }
        if (null !== $object->getBlkioDeviceWriteIOps()) {
            $values_7 = [];
            foreach ($object->getBlkioDeviceWriteIOps() as $value_7) {
                $values_7[] = $this->serializer->serialize($value_7, 'raw', $context);
            }
            $data->{'BlkioDeviceWriteIOps'} = $values_7;
        }
        if (null !== $object->getMemorySwappiness()) {
            $data->{'MemorySwappiness'} = $object->getMemorySwappiness();
        }
        if (null !== $object->getOomKillDisable()) {
            $data->{'OomKillDisable'} = $object->getOomKillDisable();
        }
        if (null !== $object->getPortBindings()) {
            $values_8 = new \stdClass();
            foreach ($object->getPortBindings() as $key_1 => $value_8) {
                $values_8->{$key_1} = $this->serializer->serialize($value_8, 'raw', $context);
            }
            $data->{'PortBindings'} = $values_8;
        }
        if (null !== $object->getPublishAllPorts()) {
            $data->{'PublishAllPorts'} = $object->getPublishAllPorts();
        }
        if (null !== $object->getPrivileged()) {
            $data->{'Privileged'} = $object->getPrivileged();
        }
        if (null !== $object->getReadonlyRootfs()) {
            $data->{'ReadonlyRootfs'} = $object->getReadonlyRootfs();
        }
        if (null !== $object->getDns()) {
            $values_9 = [];
            foreach ($object->getDns() as $value_9) {
                $values_9[] = $value_9;
            }
            $data->{'Dns'} = $values_9;
        }
        if (null !== $object->getDnsSearch()) {
            $values_10 = [];
            foreach ($object->getDnsSearch() as $value_10) {
                $values_10[] = $value_10;
            }
            $data->{'DnsSearch'} = $values_10;
        }
        if (null !== $object->getExtraHosts()) {
            $values_11 = [];
            foreach ($object->getExtraHosts() as $value_11) {
                $values_11[] = $value_11;
            }
            $data->{'ExtraHosts'} = $values_11;
        }
        if (null !== $object->getVolumesFrom()) {
            $values_12 = [];
            foreach ($object->getVolumesFrom() as $value_12) {
                $values_12[] = $value_12;
            }
            $data->{'VolumesFrom'} = $values_12;
        }
        if (null !== $object->getCapAdd()) {
            $values_13 = [];
            foreach ($object->getCapAdd() as $value_13) {
                $values_13[] = $value_13;
            }
            $data->{'CapAdd'} = $values_13;
        }
        if (null !== $object->getCapDrop()) {
            $values_14 = [];
            foreach ($object->getCapDrop() as $value_14) {
                $values_14[] = $value_14;
            }
            $data->{'CapDrop'} = $values_14;
        }
        if (null !== $object->getRestartPolicy()) {
            $data->{'RestartPolicy'} = $this->serializer->serialize($object->getRestartPolicy(), 'raw', $context);
        }
        if (null !== $object->getNetworkMode()) {
            $data->{'NetworkMode'} = $object->getNetworkMode();
        }
        if (null !== $object->getDevices()) {
            $values_15 = [];
            foreach ($object->getDevices() as $value_15) {
                $values_15[] = $this->serializer->serialize($value_15, 'raw', $context);
            }
            $data->{'Devices'} = $values_15;
        }
        if (null !== $object->getUlimits()) {
            $values_16 = [];
            foreach ($object->getUlimits() as $value_16) {
                $values_16[] = $this->serializer->serialize($value_16, 'raw', $context);
            }
            $data->{'Ulimits'} = $values_16;
        }
        if (null !== $object->getSecurityOpt()) {
            $values_17 = [];
            foreach ($object->getSecurityOpt() as $value_17) {
                $values_17[] = $value_17;
            }
            $data->{'SecurityOpt'} = $values_17;
        }
        if (null !== $object->getLogConfig()) {
            $data->{'LogConfig'} = $this->serializer->serialize($object->getLogConfig(), 'raw', $context);
        }
        if (null !== $object->getCgroupParent()) {
            $data->{'CgroupParent'} = $object->getCgroupParent();
        }

        return $data;
    }
}
