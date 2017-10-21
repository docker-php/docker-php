<?php

namespace Docker\API\Normalizer;

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
        $object = new \Docker\API\Model\HostConfig();
        if (property_exists($data, 'Binds')) {
            $value = $data->{'Binds'};
            if (is_array($data->{'Binds'})) {
                $values = [];
                foreach ($data->{'Binds'} as $value_1) {
                    $values[] = $value_1;
                }
                $value = $values;
            }
            if (is_null($data->{'Binds'})) {
                $value = $data->{'Binds'};
            }
            $object->setBinds($value);
        }
        if (property_exists($data, 'Tmpfs')) {
            $value_2 = $data->{'Tmpfs'};
            if (is_object($data->{'Tmpfs'})) {
                $values_1 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'Tmpfs'} as $key => $value_3) {
                    $values_1[$key] = $value_3;
                }
                $value_2 = $values_1;
            }
            if (is_null($data->{'Tmpfs'})) {
                $value_2 = $data->{'Tmpfs'};
            }
            $object->setTmpfs($value_2);
        }
        if (property_exists($data, 'Links')) {
            $value_4 = $data->{'Links'};
            if (is_array($data->{'Links'})) {
                $values_2 = [];
                foreach ($data->{'Links'} as $value_5) {
                    $values_2[] = $value_5;
                }
                $value_4 = $values_2;
            }
            if (is_null($data->{'Links'})) {
                $value_4 = $data->{'Links'};
            }
            $object->setLinks($value_4);
        }
        if (property_exists($data, 'LxcConf')) {
            $value_6 = $data->{'LxcConf'};
            if (is_object($data->{'LxcConf'})) {
                $values_3 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'LxcConf'} as $key_1 => $value_7) {
                    $values_3[$key_1] = $value_7;
                }
                $value_6 = $values_3;
            }
            if (is_null($data->{'LxcConf'})) {
                $value_6 = $data->{'LxcConf'};
            }
            $object->setLxcConf($value_6);
        }
        if (property_exists($data, 'Memory')) {
            $object->setMemory($data->{'Memory'});
        }
        if (property_exists($data, 'MemorySwap')) {
            $object->setMemorySwap($data->{'MemorySwap'});
        }
        if (property_exists($data, 'MemoryReservation')) {
            $object->setMemoryReservation($data->{'MemoryReservation'});
        }
        if (property_exists($data, 'KernelMemory')) {
            $object->setKernelMemory($data->{'KernelMemory'});
        }
        if (property_exists($data, 'CpuShares')) {
            $object->setCpuShares($data->{'CpuShares'});
        }
        if (property_exists($data, 'CpuPeriod')) {
            $object->setCpuPeriod($data->{'CpuPeriod'});
        }
        if (property_exists($data, 'CpuQuota')) {
            $object->setCpuQuota($data->{'CpuQuota'});
        }
        if (property_exists($data, 'CpusetCpus')) {
            $object->setCpusetCpus($data->{'CpusetCpus'});
        }
        if (property_exists($data, 'CpusetMems')) {
            $object->setCpusetMems($data->{'CpusetMems'});
        }
        if (property_exists($data, 'MaximumIOps')) {
            $object->setMaximumIOps($data->{'MaximumIOps'});
        }
        if (property_exists($data, 'MaximumIOBps')) {
            $object->setMaximumIOBps($data->{'MaximumIOBps'});
        }
        if (property_exists($data, 'BlkioWeight')) {
            $object->setBlkioWeight($data->{'BlkioWeight'});
        }
        if (property_exists($data, 'BlkioWeightDevice')) {
            $value_8 = $data->{'BlkioWeightDevice'};
            if (is_array($data->{'BlkioWeightDevice'})) {
                $values_4 = [];
                foreach ($data->{'BlkioWeightDevice'} as $value_9) {
                    $values_4[] = $this->serializer->deserialize($value_9, 'Docker\\API\\Model\\DeviceWeight', 'raw', $context);
                }
                $value_8 = $values_4;
            }
            if (is_null($data->{'BlkioWeightDevice'})) {
                $value_8 = $data->{'BlkioWeightDevice'};
            }
            $object->setBlkioWeightDevice($value_8);
        }
        if (property_exists($data, 'BlkioDeviceReadBps')) {
            $value_10 = $data->{'BlkioDeviceReadBps'};
            if (is_array($data->{'BlkioDeviceReadBps'})) {
                $values_5 = [];
                foreach ($data->{'BlkioDeviceReadBps'} as $value_11) {
                    $values_5[] = $this->serializer->deserialize($value_11, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
                }
                $value_10 = $values_5;
            }
            if (is_null($data->{'BlkioDeviceReadBps'})) {
                $value_10 = $data->{'BlkioDeviceReadBps'};
            }
            $object->setBlkioDeviceReadBps($value_10);
        }
        if (property_exists($data, 'BlkioDeviceReadIOps')) {
            $value_12 = $data->{'BlkioDeviceReadIOps'};
            if (is_array($data->{'BlkioDeviceReadIOps'})) {
                $values_6 = [];
                foreach ($data->{'BlkioDeviceReadIOps'} as $value_13) {
                    $values_6[] = $this->serializer->deserialize($value_13, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
                }
                $value_12 = $values_6;
            }
            if (is_null($data->{'BlkioDeviceReadIOps'})) {
                $value_12 = $data->{'BlkioDeviceReadIOps'};
            }
            $object->setBlkioDeviceReadIOps($value_12);
        }
        if (property_exists($data, 'BlkioDeviceWriteBps')) {
            $value_14 = $data->{'BlkioDeviceWriteBps'};
            if (is_array($data->{'BlkioDeviceWriteBps'})) {
                $values_7 = [];
                foreach ($data->{'BlkioDeviceWriteBps'} as $value_15) {
                    $values_7[] = $this->serializer->deserialize($value_15, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
                }
                $value_14 = $values_7;
            }
            if (is_null($data->{'BlkioDeviceWriteBps'})) {
                $value_14 = $data->{'BlkioDeviceWriteBps'};
            }
            $object->setBlkioDeviceWriteBps($value_14);
        }
        if (property_exists($data, 'BlkioDeviceWriteIOps')) {
            $value_16 = $data->{'BlkioDeviceWriteIOps'};
            if (is_array($data->{'BlkioDeviceWriteIOps'})) {
                $values_8 = [];
                foreach ($data->{'BlkioDeviceWriteIOps'} as $value_17) {
                    $values_8[] = $this->serializer->deserialize($value_17, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
                }
                $value_16 = $values_8;
            }
            if (is_null($data->{'BlkioDeviceWriteIOps'})) {
                $value_16 = $data->{'BlkioDeviceWriteIOps'};
            }
            $object->setBlkioDeviceWriteIOps($value_16);
        }
        if (property_exists($data, 'MemorySwappiness')) {
            $object->setMemorySwappiness($data->{'MemorySwappiness'});
        }
        if (property_exists($data, 'OomKillDisable')) {
            $object->setOomKillDisable($data->{'OomKillDisable'});
        }
        if (property_exists($data, 'OomScoreAdj')) {
            $object->setOomScoreAdj($data->{'OomScoreAdj'});
        }
        if (property_exists($data, 'PidsLimit')) {
            $object->setPidsLimit($data->{'PidsLimit'});
        }
        if (property_exists($data, 'PortBindings')) {
            $value_18 = $data->{'PortBindings'};
            if (is_object($data->{'PortBindings'})) {
                $values_9 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'PortBindings'} as $key_2 => $value_19) {
                    $value_20 = $value_19;
                    if (is_array($value_19)) {
                        $values_10 = [];
                        foreach ($value_19 as $value_21) {
                            $values_10[] = $this->serializer->deserialize($value_21, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
                        }
                        $value_20 = $values_10;
                    }
                    if (is_null($value_19)) {
                        $value_20 = $value_19;
                    }
                    $values_9[$key_2] = $value_20;
                }
                $value_18 = $values_9;
            }
            if (is_null($data->{'PortBindings'})) {
                $value_18 = $data->{'PortBindings'};
            }
            $object->setPortBindings($value_18);
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
        if (property_exists($data, 'Sysctls')) {
            $value_22 = $data->{'Sysctls'};
            if (is_object($data->{'Sysctls'})) {
                $values_11 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'Sysctls'} as $key_3 => $value_23) {
                    $values_11[$key_3] = $value_23;
                }
                $value_22 = $values_11;
            }
            if (is_null($data->{'Sysctls'})) {
                $value_22 = $data->{'Sysctls'};
            }
            $object->setSysctls($value_22);
        }
        if (property_exists($data, 'StorageOpt')) {
            $value_24 = $data->{'StorageOpt'};
            if (is_object($data->{'StorageOpt'})) {
                $values_12 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'StorageOpt'} as $key_4 => $value_25) {
                    $values_12[$key_4] = $value_25;
                }
                $value_24 = $values_12;
            }
            if (is_null($data->{'StorageOpt'})) {
                $value_24 = $data->{'StorageOpt'};
            }
            $object->setStorageOpt($value_24);
        }
        if (property_exists($data, 'Dns')) {
            $value_26 = $data->{'Dns'};
            if (is_array($data->{'Dns'})) {
                $values_13 = [];
                foreach ($data->{'Dns'} as $value_27) {
                    $values_13[] = $value_27;
                }
                $value_26 = $values_13;
            }
            if (is_null($data->{'Dns'})) {
                $value_26 = $data->{'Dns'};
            }
            $object->setDns($value_26);
        }
        if (property_exists($data, 'DnsOptions')) {
            $value_28 = $data->{'DnsOptions'};
            if (is_array($data->{'DnsOptions'})) {
                $values_14 = [];
                foreach ($data->{'DnsOptions'} as $value_29) {
                    $values_14[] = $value_29;
                }
                $value_28 = $values_14;
            }
            if (is_null($data->{'DnsOptions'})) {
                $value_28 = $data->{'DnsOptions'};
            }
            $object->setDnsOptions($value_28);
        }
        if (property_exists($data, 'DnsSearch')) {
            $value_30 = $data->{'DnsSearch'};
            if (is_array($data->{'DnsSearch'})) {
                $values_15 = [];
                foreach ($data->{'DnsSearch'} as $value_31) {
                    $values_15[] = $value_31;
                }
                $value_30 = $values_15;
            }
            if (is_null($data->{'DnsSearch'})) {
                $value_30 = $data->{'DnsSearch'};
            }
            $object->setDnsSearch($value_30);
        }
        if (property_exists($data, 'ExtraHosts')) {
            $value_32 = $data->{'ExtraHosts'};
            if (is_array($data->{'ExtraHosts'})) {
                $values_16 = [];
                foreach ($data->{'ExtraHosts'} as $value_33) {
                    $values_16[] = $value_33;
                }
                $value_32 = $values_16;
            }
            if (is_null($data->{'ExtraHosts'})) {
                $value_32 = $data->{'ExtraHosts'};
            }
            $object->setExtraHosts($value_32);
        }
        if (property_exists($data, 'VolumesFrom')) {
            $value_34 = $data->{'VolumesFrom'};
            if (is_array($data->{'VolumesFrom'})) {
                $values_17 = [];
                foreach ($data->{'VolumesFrom'} as $value_35) {
                    $values_17[] = $value_35;
                }
                $value_34 = $values_17;
            }
            if (is_null($data->{'VolumesFrom'})) {
                $value_34 = $data->{'VolumesFrom'};
            }
            $object->setVolumesFrom($value_34);
        }
        if (property_exists($data, 'CapAdd')) {
            $value_36 = $data->{'CapAdd'};
            if (is_array($data->{'CapAdd'})) {
                $values_18 = [];
                foreach ($data->{'CapAdd'} as $value_37) {
                    $values_18[] = $value_37;
                }
                $value_36 = $values_18;
            }
            if (is_null($data->{'CapAdd'})) {
                $value_36 = $data->{'CapAdd'};
            }
            $object->setCapAdd($value_36);
        }
        if (property_exists($data, 'CapDrop')) {
            $value_38 = $data->{'CapDrop'};
            if (is_array($data->{'CapDrop'})) {
                $values_19 = [];
                foreach ($data->{'CapDrop'} as $value_39) {
                    $values_19[] = $value_39;
                }
                $value_38 = $values_19;
            }
            if (is_null($data->{'CapDrop'})) {
                $value_38 = $data->{'CapDrop'};
            }
            $object->setCapDrop($value_38);
        }
        if (property_exists($data, 'GroupAdd')) {
            $value_40 = $data->{'GroupAdd'};
            if (is_array($data->{'GroupAdd'})) {
                $values_20 = [];
                foreach ($data->{'GroupAdd'} as $value_41) {
                    $values_20[] = $value_41;
                }
                $value_40 = $values_20;
            }
            if (is_null($data->{'GroupAdd'})) {
                $value_40 = $data->{'GroupAdd'};
            }
            $object->setGroupAdd($value_40);
        }
        if (property_exists($data, 'RestartPolicy')) {
            $object->setRestartPolicy($this->serializer->deserialize($data->{'RestartPolicy'}, 'Docker\\API\\Model\\RestartPolicy', 'raw', $context));
        }
        if (property_exists($data, 'UsernsMode')) {
            $object->setUsernsMode($data->{'UsernsMode'});
        }
        if (property_exists($data, 'NetworkMode')) {
            $object->setNetworkMode($data->{'NetworkMode'});
        }
        if (property_exists($data, 'Devices')) {
            $value_42 = $data->{'Devices'};
            if (is_array($data->{'Devices'})) {
                $values_21 = [];
                foreach ($data->{'Devices'} as $value_43) {
                    $values_21[] = $this->serializer->deserialize($value_43, 'Docker\\API\\Model\\Device', 'raw', $context);
                }
                $value_42 = $values_21;
            }
            if (is_null($data->{'Devices'})) {
                $value_42 = $data->{'Devices'};
            }
            $object->setDevices($value_42);
        }
        if (property_exists($data, 'Ulimits')) {
            $value_44 = $data->{'Ulimits'};
            if (is_array($data->{'Ulimits'})) {
                $values_22 = [];
                foreach ($data->{'Ulimits'} as $value_45) {
                    $values_22[] = $this->serializer->deserialize($value_45, 'Docker\\API\\Model\\Ulimit', 'raw', $context);
                }
                $value_44 = $values_22;
            }
            if (is_null($data->{'Ulimits'})) {
                $value_44 = $data->{'Ulimits'};
            }
            $object->setUlimits($value_44);
        }
        if (property_exists($data, 'SecurityOpt')) {
            $value_46 = $data->{'SecurityOpt'};
            if (is_array($data->{'SecurityOpt'})) {
                $values_23 = [];
                foreach ($data->{'SecurityOpt'} as $value_47) {
                    $values_23[] = $value_47;
                }
                $value_46 = $values_23;
            }
            if (is_null($data->{'SecurityOpt'})) {
                $value_46 = $data->{'SecurityOpt'};
            }
            $object->setSecurityOpt($value_46);
        }
        if (property_exists($data, 'LogConfig')) {
            $object->setLogConfig($this->serializer->deserialize($data->{'LogConfig'}, 'Docker\\API\\Model\\LogConfig', 'raw', $context));
        }
        if (property_exists($data, 'CgroupParent')) {
            $object->setCgroupParent($data->{'CgroupParent'});
        }
        if (property_exists($data, 'VolumeDriver')) {
            $object->setVolumeDriver($data->{'VolumeDriver'});
        }
        if (property_exists($data, 'ShmSize')) {
            $object->setShmSize($data->{'ShmSize'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data  = new \stdClass();
        $value = $object->getBinds();
        if (is_array($object->getBinds())) {
            $values = [];
            foreach ($object->getBinds() as $value_1) {
                $values[] = $value_1;
            }
            $value = $values;
        }
        if (is_null($object->getBinds())) {
            $value = $object->getBinds();
        }
        $data->{'Binds'} = $value;
        $value_2         = $object->getTmpfs();
        if (is_object($object->getTmpfs())) {
            $values_1 = new \stdClass();
            foreach ($object->getTmpfs() as $key => $value_3) {
                $values_1->{$key} = $value_3;
            }
            $value_2 = $values_1;
        }
        if (is_null($object->getTmpfs())) {
            $value_2 = $object->getTmpfs();
        }
        $data->{'Tmpfs'} = $value_2;
        $value_4         = $object->getLinks();
        if (is_array($object->getLinks())) {
            $values_2 = [];
            foreach ($object->getLinks() as $value_5) {
                $values_2[] = $value_5;
            }
            $value_4 = $values_2;
        }
        if (is_null($object->getLinks())) {
            $value_4 = $object->getLinks();
        }
        $data->{'Links'} = $value_4;
        $value_6         = $object->getLxcConf();
        if (is_object($object->getLxcConf())) {
            $values_3 = new \stdClass();
            foreach ($object->getLxcConf() as $key_1 => $value_7) {
                $values_3->{$key_1} = $value_7;
            }
            $value_6 = $values_3;
        }
        if (is_null($object->getLxcConf())) {
            $value_6 = $object->getLxcConf();
        }
        $data->{'LxcConf'} = $value_6;
        if (null !== $object->getMemory()) {
            $data->{'Memory'} = $object->getMemory();
        }
        if (null !== $object->getMemorySwap()) {
            $data->{'MemorySwap'} = $object->getMemorySwap();
        }
        if (null !== $object->getMemoryReservation()) {
            $data->{'MemoryReservation'} = $object->getMemoryReservation();
        }
        if (null !== $object->getKernelMemory()) {
            $data->{'KernelMemory'} = $object->getKernelMemory();
        }
        if (null !== $object->getCpuShares()) {
            $data->{'CpuShares'} = $object->getCpuShares();
        }
        if (null !== $object->getCpuPeriod()) {
            $data->{'CpuPeriod'} = $object->getCpuPeriod();
        }
        if (null !== $object->getCpuQuota()) {
            $data->{'CpuQuota'} = $object->getCpuQuota();
        }
        if (null !== $object->getCpusetCpus()) {
            $data->{'CpusetCpus'} = $object->getCpusetCpus();
        }
        if (null !== $object->getCpusetMems()) {
            $data->{'CpusetMems'} = $object->getCpusetMems();
        }
        if (null !== $object->getMaximumIOps()) {
            $data->{'MaximumIOps'} = $object->getMaximumIOps();
        }
        if (null !== $object->getMaximumIOBps()) {
            $data->{'MaximumIOBps'} = $object->getMaximumIOBps();
        }
        if (null !== $object->getBlkioWeight()) {
            $data->{'BlkioWeight'} = $object->getBlkioWeight();
        }
        $value_8 = $object->getBlkioWeightDevice();
        if (is_array($object->getBlkioWeightDevice())) {
            $values_4 = [];
            foreach ($object->getBlkioWeightDevice() as $value_9) {
                $values_4[] = $this->serializer->serialize($value_9, 'raw', $context);
            }
            $value_8 = $values_4;
        }
        if (is_null($object->getBlkioWeightDevice())) {
            $value_8 = $object->getBlkioWeightDevice();
        }
        $data->{'BlkioWeightDevice'} = $value_8;
        $value_10                    = $object->getBlkioDeviceReadBps();
        if (is_array($object->getBlkioDeviceReadBps())) {
            $values_5 = [];
            foreach ($object->getBlkioDeviceReadBps() as $value_11) {
                $values_5[] = $this->serializer->serialize($value_11, 'raw', $context);
            }
            $value_10 = $values_5;
        }
        if (is_null($object->getBlkioDeviceReadBps())) {
            $value_10 = $object->getBlkioDeviceReadBps();
        }
        $data->{'BlkioDeviceReadBps'} = $value_10;
        $value_12                     = $object->getBlkioDeviceReadIOps();
        if (is_array($object->getBlkioDeviceReadIOps())) {
            $values_6 = [];
            foreach ($object->getBlkioDeviceReadIOps() as $value_13) {
                $values_6[] = $this->serializer->serialize($value_13, 'raw', $context);
            }
            $value_12 = $values_6;
        }
        if (is_null($object->getBlkioDeviceReadIOps())) {
            $value_12 = $object->getBlkioDeviceReadIOps();
        }
        $data->{'BlkioDeviceReadIOps'} = $value_12;
        $value_14                      = $object->getBlkioDeviceWriteBps();
        if (is_array($object->getBlkioDeviceWriteBps())) {
            $values_7 = [];
            foreach ($object->getBlkioDeviceWriteBps() as $value_15) {
                $values_7[] = $this->serializer->serialize($value_15, 'raw', $context);
            }
            $value_14 = $values_7;
        }
        if (is_null($object->getBlkioDeviceWriteBps())) {
            $value_14 = $object->getBlkioDeviceWriteBps();
        }
        $data->{'BlkioDeviceWriteBps'} = $value_14;
        $value_16                      = $object->getBlkioDeviceWriteIOps();
        if (is_array($object->getBlkioDeviceWriteIOps())) {
            $values_8 = [];
            foreach ($object->getBlkioDeviceWriteIOps() as $value_17) {
                $values_8[] = $this->serializer->serialize($value_17, 'raw', $context);
            }
            $value_16 = $values_8;
        }
        if (is_null($object->getBlkioDeviceWriteIOps())) {
            $value_16 = $object->getBlkioDeviceWriteIOps();
        }
        $data->{'BlkioDeviceWriteIOps'} = $value_16;
        if (null !== $object->getMemorySwappiness()) {
            $data->{'MemorySwappiness'} = $object->getMemorySwappiness();
        }
        if (null !== $object->getOomKillDisable()) {
            $data->{'OomKillDisable'} = $object->getOomKillDisable();
        }
        if (null !== $object->getOomScoreAdj()) {
            $data->{'OomScoreAdj'} = $object->getOomScoreAdj();
        }
        if (null !== $object->getPidsLimit()) {
            $data->{'PidsLimit'} = $object->getPidsLimit();
        }
        $value_18 = $object->getPortBindings();
        if (is_object($object->getPortBindings())) {
            $values_9 = new \stdClass();
            foreach ($object->getPortBindings() as $key_2 => $value_19) {
                $value_20 = $value_19;
                if (is_array($value_19)) {
                    $values_10 = [];
                    foreach ($value_19 as $value_21) {
                        $values_10[] = $this->serializer->serialize($value_21, 'raw', $context);
                    }
                    $value_20 = $values_10;
                }
                if (is_null($value_19)) {
                    $value_20 = $value_19;
                }
                $values_9->{$key_2} = $value_20;
            }
            $value_18 = $values_9;
        }
        if (is_null($object->getPortBindings())) {
            $value_18 = $object->getPortBindings();
        }
        $data->{'PortBindings'} = $value_18;
        if (null !== $object->getPublishAllPorts()) {
            $data->{'PublishAllPorts'} = $object->getPublishAllPorts();
        }
        if (null !== $object->getPrivileged()) {
            $data->{'Privileged'} = $object->getPrivileged();
        }
        if (null !== $object->getReadonlyRootfs()) {
            $data->{'ReadonlyRootfs'} = $object->getReadonlyRootfs();
        }
        $value_22 = $object->getSysctls();
        if (is_object($object->getSysctls())) {
            $values_11 = new \stdClass();
            foreach ($object->getSysctls() as $key_3 => $value_23) {
                $values_11->{$key_3} = $value_23;
            }
            $value_22 = $values_11;
        }
        if (is_null($object->getSysctls())) {
            $value_22 = $object->getSysctls();
        }
        $data->{'Sysctls'} = $value_22;
        $value_24          = $object->getStorageOpt();
        if (is_object($object->getStorageOpt())) {
            $values_12 = new \stdClass();
            foreach ($object->getStorageOpt() as $key_4 => $value_25) {
                $values_12->{$key_4} = $value_25;
            }
            $value_24 = $values_12;
        }
        if (is_null($object->getStorageOpt())) {
            $value_24 = $object->getStorageOpt();
        }
        $data->{'StorageOpt'} = $value_24;
        $value_26             = $object->getDns();
        if (is_array($object->getDns())) {
            $values_13 = [];
            foreach ($object->getDns() as $value_27) {
                $values_13[] = $value_27;
            }
            $value_26 = $values_13;
        }
        if (is_null($object->getDns())) {
            $value_26 = $object->getDns();
        }
        $data->{'Dns'} = $value_26;
        $value_28      = $object->getDnsOptions();
        if (is_array($object->getDnsOptions())) {
            $values_14 = [];
            foreach ($object->getDnsOptions() as $value_29) {
                $values_14[] = $value_29;
            }
            $value_28 = $values_14;
        }
        if (is_null($object->getDnsOptions())) {
            $value_28 = $object->getDnsOptions();
        }
        $data->{'DnsOptions'} = $value_28;
        $value_30             = $object->getDnsSearch();
        if (is_array($object->getDnsSearch())) {
            $values_15 = [];
            foreach ($object->getDnsSearch() as $value_31) {
                $values_15[] = $value_31;
            }
            $value_30 = $values_15;
        }
        if (is_null($object->getDnsSearch())) {
            $value_30 = $object->getDnsSearch();
        }
        $data->{'DnsSearch'} = $value_30;
        $value_32            = $object->getExtraHosts();
        if (is_array($object->getExtraHosts())) {
            $values_16 = [];
            foreach ($object->getExtraHosts() as $value_33) {
                $values_16[] = $value_33;
            }
            $value_32 = $values_16;
        }
        if (is_null($object->getExtraHosts())) {
            $value_32 = $object->getExtraHosts();
        }
        $data->{'ExtraHosts'} = $value_32;
        $value_34             = $object->getVolumesFrom();
        if (is_array($object->getVolumesFrom())) {
            $values_17 = [];
            foreach ($object->getVolumesFrom() as $value_35) {
                $values_17[] = $value_35;
            }
            $value_34 = $values_17;
        }
        if (is_null($object->getVolumesFrom())) {
            $value_34 = $object->getVolumesFrom();
        }
        $data->{'VolumesFrom'} = $value_34;
        $value_36              = $object->getCapAdd();
        if (is_array($object->getCapAdd())) {
            $values_18 = [];
            foreach ($object->getCapAdd() as $value_37) {
                $values_18[] = $value_37;
            }
            $value_36 = $values_18;
        }
        if (is_null($object->getCapAdd())) {
            $value_36 = $object->getCapAdd();
        }
        $data->{'CapAdd'} = $value_36;
        $value_38         = $object->getCapDrop();
        if (is_array($object->getCapDrop())) {
            $values_19 = [];
            foreach ($object->getCapDrop() as $value_39) {
                $values_19[] = $value_39;
            }
            $value_38 = $values_19;
        }
        if (is_null($object->getCapDrop())) {
            $value_38 = $object->getCapDrop();
        }
        $data->{'CapDrop'} = $value_38;
        $value_40          = $object->getGroupAdd();
        if (is_array($object->getGroupAdd())) {
            $values_20 = [];
            foreach ($object->getGroupAdd() as $value_41) {
                $values_20[] = $value_41;
            }
            $value_40 = $values_20;
        }
        if (is_null($object->getGroupAdd())) {
            $value_40 = $object->getGroupAdd();
        }
        $data->{'GroupAdd'} = $value_40;
        if (null !== $object->getRestartPolicy()) {
            $data->{'RestartPolicy'} = $this->serializer->serialize($object->getRestartPolicy(), 'raw', $context);
        }
        if (null !== $object->getUsernsMode()) {
            $data->{'UsernsMode'} = $object->getUsernsMode();
        }
        if (null !== $object->getNetworkMode()) {
            $data->{'NetworkMode'} = $object->getNetworkMode();
        }
        $value_42 = $object->getDevices();
        if (is_array($object->getDevices())) {
            $values_21 = [];
            foreach ($object->getDevices() as $value_43) {
                $values_21[] = $this->serializer->serialize($value_43, 'raw', $context);
            }
            $value_42 = $values_21;
        }
        if (is_null($object->getDevices())) {
            $value_42 = $object->getDevices();
        }
        $data->{'Devices'} = $value_42;
        $value_44          = $object->getUlimits();
        if (is_array($object->getUlimits())) {
            $values_22 = [];
            foreach ($object->getUlimits() as $value_45) {
                $values_22[] = $this->serializer->serialize($value_45, 'raw', $context);
            }
            $value_44 = $values_22;
        }
        if (is_null($object->getUlimits())) {
            $value_44 = $object->getUlimits();
        }
        $data->{'Ulimits'} = $value_44;
        $value_46          = $object->getSecurityOpt();
        if (is_array($object->getSecurityOpt())) {
            $values_23 = [];
            foreach ($object->getSecurityOpt() as $value_47) {
                $values_23[] = $value_47;
            }
            $value_46 = $values_23;
        }
        if (is_null($object->getSecurityOpt())) {
            $value_46 = $object->getSecurityOpt();
        }
        $data->{'SecurityOpt'} = $value_46;
        if (null !== $object->getLogConfig()) {
            $data->{'LogConfig'} = $this->serializer->serialize($object->getLogConfig(), 'raw', $context);
        }
        if (null !== $object->getCgroupParent()) {
            $data->{'CgroupParent'} = $object->getCgroupParent();
        }
        if (null !== $object->getVolumeDriver()) {
            $data->{'VolumeDriver'} = $object->getVolumeDriver();
        }
        if (null !== $object->getShmSize()) {
            $data->{'ShmSize'} = $object->getShmSize();
        }

        return $data;
    }
}
