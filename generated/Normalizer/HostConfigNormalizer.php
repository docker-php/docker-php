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
        if (property_exists($data, 'Links')) {
            $value_2 = $data->{'Links'};
            if (is_array($data->{'Links'})) {
                $values_1 = [];
                foreach ($data->{'Links'} as $value_3) {
                    $values_1[] = $value_3;
                }
                $value_2 = $values_1;
            }
            if (is_null($data->{'Links'})) {
                $value_2 = $data->{'Links'};
            }
            $object->setLinks($value_2);
        }
        if (property_exists($data, 'LxcConf')) {
            $values_2 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'LxcConf'} as $key => $value_4) {
                $values_2[$key] = $value_4;
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
            $value_5 = $data->{'BlkioWeightDevice'};
            if (is_array($data->{'BlkioWeightDevice'})) {
                $values_3 = [];
                foreach ($data->{'BlkioWeightDevice'} as $value_6) {
                    $values_3[] = $this->serializer->deserialize($value_6, 'Docker\\API\\Model\\DeviceWeight', 'raw', $context);
                }
                $value_5 = $values_3;
            }
            if (is_null($data->{'BlkioWeightDevice'})) {
                $value_5 = $data->{'BlkioWeightDevice'};
            }
            $object->setBlkioWeightDevice($value_5);
        }
        if (property_exists($data, 'BlkioDeviceReadBps')) {
            $value_7 = $data->{'BlkioDeviceReadBps'};
            if (is_array($data->{'BlkioDeviceReadBps'})) {
                $values_4 = [];
                foreach ($data->{'BlkioDeviceReadBps'} as $value_8) {
                    $values_4[] = $this->serializer->deserialize($value_8, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
                }
                $value_7 = $values_4;
            }
            if (is_null($data->{'BlkioDeviceReadBps'})) {
                $value_7 = $data->{'BlkioDeviceReadBps'};
            }
            $object->setBlkioDeviceReadBps($value_7);
        }
        if (property_exists($data, 'BlkioDeviceReadIOps')) {
            $value_9 = $data->{'BlkioDeviceReadIOps'};
            if (is_array($data->{'BlkioDeviceReadIOps'})) {
                $values_5 = [];
                foreach ($data->{'BlkioDeviceReadIOps'} as $value_10) {
                    $values_5[] = $this->serializer->deserialize($value_10, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
                }
                $value_9 = $values_5;
            }
            if (is_null($data->{'BlkioDeviceReadIOps'})) {
                $value_9 = $data->{'BlkioDeviceReadIOps'};
            }
            $object->setBlkioDeviceReadIOps($value_9);
        }
        if (property_exists($data, 'BlkioDeviceWriteBps')) {
            $value_11 = $data->{'BlkioDeviceWriteBps'};
            if (is_array($data->{'BlkioDeviceWriteBps'})) {
                $values_6 = [];
                foreach ($data->{'BlkioDeviceWriteBps'} as $value_12) {
                    $values_6[] = $this->serializer->deserialize($value_12, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
                }
                $value_11 = $values_6;
            }
            if (is_null($data->{'BlkioDeviceWriteBps'})) {
                $value_11 = $data->{'BlkioDeviceWriteBps'};
            }
            $object->setBlkioDeviceWriteBps($value_11);
        }
        if (property_exists($data, 'BlkioDeviceWriteIOps')) {
            $value_13 = $data->{'BlkioDeviceWriteIOps'};
            if (is_array($data->{'BlkioDeviceWriteIOps'})) {
                $values_7 = [];
                foreach ($data->{'BlkioDeviceWriteIOps'} as $value_14) {
                    $values_7[] = $this->serializer->deserialize($value_14, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
                }
                $value_13 = $values_7;
            }
            if (is_null($data->{'BlkioDeviceWriteIOps'})) {
                $value_13 = $data->{'BlkioDeviceWriteIOps'};
            }
            $object->setBlkioDeviceWriteIOps($value_13);
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
            $value_15 = $data->{'PortBindings'};
            if (is_object($data->{'PortBindings'})) {
                $values_8 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
                foreach ($data->{'PortBindings'} as $key_1 => $value_16) {
                    $value_17 = $value_16;
                    if (is_array($value_16)) {
                        $values_9 = [];
                        foreach ($value_16 as $value_18) {
                            $values_9[] = $this->serializer->deserialize($value_18, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
                        }
                        $value_17 = $values_9;
                    }
                    if (is_null($value_16)) {
                        $value_17 = $value_16;
                    }
                    $values_8[$key_1] = $value_17;
                }
                $value_15 = $values_8;
            }
            if (is_null($data->{'PortBindings'})) {
                $value_15 = $data->{'PortBindings'};
            }
            $object->setPortBindings($value_15);
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
            $value_19 = $data->{'Dns'};
            if (is_array($data->{'Dns'})) {
                $values_10 = [];
                foreach ($data->{'Dns'} as $value_20) {
                    $values_10[] = $value_20;
                }
                $value_19 = $values_10;
            }
            if (is_null($data->{'Dns'})) {
                $value_19 = $data->{'Dns'};
            }
            $object->setDns($value_19);
        }
        if (property_exists($data, 'DnsOptions')) {
            $value_21 = $data->{'DnsOptions'};
            if (is_array($data->{'DnsOptions'})) {
                $values_11 = [];
                foreach ($data->{'DnsOptions'} as $value_22) {
                    $values_11[] = $value_22;
                }
                $value_21 = $values_11;
            }
            if (is_null($data->{'DnsOptions'})) {
                $value_21 = $data->{'DnsOptions'};
            }
            $object->setDnsOptions($value_21);
        }
        if (property_exists($data, 'DnsSearch')) {
            $value_23 = $data->{'DnsSearch'};
            if (is_array($data->{'DnsSearch'})) {
                $values_12 = [];
                foreach ($data->{'DnsSearch'} as $value_24) {
                    $values_12[] = $value_24;
                }
                $value_23 = $values_12;
            }
            if (is_null($data->{'DnsSearch'})) {
                $value_23 = $data->{'DnsSearch'};
            }
            $object->setDnsSearch($value_23);
        }
        if (property_exists($data, 'ExtraHosts')) {
            $value_25 = $data->{'ExtraHosts'};
            if (is_array($data->{'ExtraHosts'})) {
                $values_13 = [];
                foreach ($data->{'ExtraHosts'} as $value_26) {
                    $values_13[] = $value_26;
                }
                $value_25 = $values_13;
            }
            if (is_null($data->{'ExtraHosts'})) {
                $value_25 = $data->{'ExtraHosts'};
            }
            $object->setExtraHosts($value_25);
        }
        if (property_exists($data, 'VolumesFrom')) {
            $value_27 = $data->{'VolumesFrom'};
            if (is_array($data->{'VolumesFrom'})) {
                $values_14 = [];
                foreach ($data->{'VolumesFrom'} as $value_28) {
                    $values_14[] = $value_28;
                }
                $value_27 = $values_14;
            }
            if (is_null($data->{'VolumesFrom'})) {
                $value_27 = $data->{'VolumesFrom'};
            }
            $object->setVolumesFrom($value_27);
        }
        if (property_exists($data, 'CapAdd')) {
            $value_29 = $data->{'CapAdd'};
            if (is_array($data->{'CapAdd'})) {
                $values_15 = [];
                foreach ($data->{'CapAdd'} as $value_30) {
                    $values_15[] = $value_30;
                }
                $value_29 = $values_15;
            }
            if (is_null($data->{'CapAdd'})) {
                $value_29 = $data->{'CapAdd'};
            }
            $object->setCapAdd($value_29);
        }
        if (property_exists($data, 'CapDrop')) {
            $value_31 = $data->{'CapDrop'};
            if (is_array($data->{'CapDrop'})) {
                $values_16 = [];
                foreach ($data->{'CapDrop'} as $value_32) {
                    $values_16[] = $value_32;
                }
                $value_31 = $values_16;
            }
            if (is_null($data->{'CapDrop'})) {
                $value_31 = $data->{'CapDrop'};
            }
            $object->setCapDrop($value_31);
        }
        if (property_exists($data, 'GroupAdd')) {
            $value_33 = $data->{'GroupAdd'};
            if (is_array($data->{'GroupAdd'})) {
                $values_17 = [];
                foreach ($data->{'GroupAdd'} as $value_34) {
                    $values_17[] = $value_34;
                }
                $value_33 = $values_17;
            }
            if (is_null($data->{'GroupAdd'})) {
                $value_33 = $data->{'GroupAdd'};
            }
            $object->setGroupAdd($value_33);
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
            $value_35 = $data->{'Devices'};
            if (is_array($data->{'Devices'})) {
                $values_18 = [];
                foreach ($data->{'Devices'} as $value_36) {
                    $values_18[] = $this->serializer->deserialize($value_36, 'Docker\\API\\Model\\Device', 'raw', $context);
                }
                $value_35 = $values_18;
            }
            if (is_null($data->{'Devices'})) {
                $value_35 = $data->{'Devices'};
            }
            $object->setDevices($value_35);
        }
        if (property_exists($data, 'Ulimits')) {
            $value_37 = $data->{'Ulimits'};
            if (is_array($data->{'Ulimits'})) {
                $values_19 = [];
                foreach ($data->{'Ulimits'} as $value_38) {
                    $values_19[] = $this->serializer->deserialize($value_38, 'Docker\\API\\Model\\Ulimit', 'raw', $context);
                }
                $value_37 = $values_19;
            }
            if (is_null($data->{'Ulimits'})) {
                $value_37 = $data->{'Ulimits'};
            }
            $object->setUlimits($value_37);
        }
        if (property_exists($data, 'SecurityOpt')) {
            $value_39 = $data->{'SecurityOpt'};
            if (is_array($data->{'SecurityOpt'})) {
                $values_20 = [];
                foreach ($data->{'SecurityOpt'} as $value_40) {
                    $values_20[] = $value_40;
                }
                $value_39 = $values_20;
            }
            if (is_null($data->{'SecurityOpt'})) {
                $value_39 = $data->{'SecurityOpt'};
            }
            $object->setSecurityOpt($value_39);
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
        $value_2         = $object->getLinks();
        if (is_array($object->getLinks())) {
            $values_1 = [];
            foreach ($object->getLinks() as $value_3) {
                $values_1[] = $value_3;
            }
            $value_2 = $values_1;
        }
        if (is_null($object->getLinks())) {
            $value_2 = $object->getLinks();
        }
        $data->{'Links'} = $value_2;
        if (null !== $object->getLxcConf()) {
            $values_2 = new \stdClass();
            foreach ($object->getLxcConf() as $key => $value_4) {
                $values_2->{$key} = $value_4;
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
        $value_5 = $object->getBlkioWeightDevice();
        if (is_array($object->getBlkioWeightDevice())) {
            $values_3 = [];
            foreach ($object->getBlkioWeightDevice() as $value_6) {
                $values_3[] = $this->serializer->serialize($value_6, 'raw', $context);
            }
            $value_5 = $values_3;
        }
        if (is_null($object->getBlkioWeightDevice())) {
            $value_5 = $object->getBlkioWeightDevice();
        }
        $data->{'BlkioWeightDevice'} = $value_5;
        $value_7                     = $object->getBlkioDeviceReadBps();
        if (is_array($object->getBlkioDeviceReadBps())) {
            $values_4 = [];
            foreach ($object->getBlkioDeviceReadBps() as $value_8) {
                $values_4[] = $this->serializer->serialize($value_8, 'raw', $context);
            }
            $value_7 = $values_4;
        }
        if (is_null($object->getBlkioDeviceReadBps())) {
            $value_7 = $object->getBlkioDeviceReadBps();
        }
        $data->{'BlkioDeviceReadBps'} = $value_7;
        $value_9                      = $object->getBlkioDeviceReadIOps();
        if (is_array($object->getBlkioDeviceReadIOps())) {
            $values_5 = [];
            foreach ($object->getBlkioDeviceReadIOps() as $value_10) {
                $values_5[] = $this->serializer->serialize($value_10, 'raw', $context);
            }
            $value_9 = $values_5;
        }
        if (is_null($object->getBlkioDeviceReadIOps())) {
            $value_9 = $object->getBlkioDeviceReadIOps();
        }
        $data->{'BlkioDeviceReadIOps'} = $value_9;
        $value_11                      = $object->getBlkioDeviceWriteBps();
        if (is_array($object->getBlkioDeviceWriteBps())) {
            $values_6 = [];
            foreach ($object->getBlkioDeviceWriteBps() as $value_12) {
                $values_6[] = $this->serializer->serialize($value_12, 'raw', $context);
            }
            $value_11 = $values_6;
        }
        if (is_null($object->getBlkioDeviceWriteBps())) {
            $value_11 = $object->getBlkioDeviceWriteBps();
        }
        $data->{'BlkioDeviceWriteBps'} = $value_11;
        $value_13                      = $object->getBlkioDeviceWriteIOps();
        if (is_array($object->getBlkioDeviceWriteIOps())) {
            $values_7 = [];
            foreach ($object->getBlkioDeviceWriteIOps() as $value_14) {
                $values_7[] = $this->serializer->serialize($value_14, 'raw', $context);
            }
            $value_13 = $values_7;
        }
        if (is_null($object->getBlkioDeviceWriteIOps())) {
            $value_13 = $object->getBlkioDeviceWriteIOps();
        }
        $data->{'BlkioDeviceWriteIOps'} = $value_13;
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
        $value_15 = $object->getPortBindings();
        if (is_object($object->getPortBindings())) {
            $values_8 = new \stdClass();
            foreach ($object->getPortBindings() as $key_1 => $value_16) {
                $value_17 = $value_16;
                if (is_array($value_16)) {
                    $values_9 = [];
                    foreach ($value_16 as $value_18) {
                        $values_9[] = $this->serializer->serialize($value_18, 'raw', $context);
                    }
                    $value_17 = $values_9;
                }
                if (is_null($value_16)) {
                    $value_17 = $value_16;
                }
                $values_8->{$key_1} = $value_17;
            }
            $value_15 = $values_8;
        }
        if (is_null($object->getPortBindings())) {
            $value_15 = $object->getPortBindings();
        }
        $data->{'PortBindings'} = $value_15;
        if (null !== $object->getPublishAllPorts()) {
            $data->{'PublishAllPorts'} = $object->getPublishAllPorts();
        }
        if (null !== $object->getPrivileged()) {
            $data->{'Privileged'} = $object->getPrivileged();
        }
        if (null !== $object->getReadonlyRootfs()) {
            $data->{'ReadonlyRootfs'} = $object->getReadonlyRootfs();
        }
        $value_19 = $object->getDns();
        if (is_array($object->getDns())) {
            $values_10 = [];
            foreach ($object->getDns() as $value_20) {
                $values_10[] = $value_20;
            }
            $value_19 = $values_10;
        }
        if (is_null($object->getDns())) {
            $value_19 = $object->getDns();
        }
        $data->{'Dns'} = $value_19;
        $value_21      = $object->getDnsOptions();
        if (is_array($object->getDnsOptions())) {
            $values_11 = [];
            foreach ($object->getDnsOptions() as $value_22) {
                $values_11[] = $value_22;
            }
            $value_21 = $values_11;
        }
        if (is_null($object->getDnsOptions())) {
            $value_21 = $object->getDnsOptions();
        }
        $data->{'DnsOptions'} = $value_21;
        $value_23             = $object->getDnsSearch();
        if (is_array($object->getDnsSearch())) {
            $values_12 = [];
            foreach ($object->getDnsSearch() as $value_24) {
                $values_12[] = $value_24;
            }
            $value_23 = $values_12;
        }
        if (is_null($object->getDnsSearch())) {
            $value_23 = $object->getDnsSearch();
        }
        $data->{'DnsSearch'} = $value_23;
        $value_25            = $object->getExtraHosts();
        if (is_array($object->getExtraHosts())) {
            $values_13 = [];
            foreach ($object->getExtraHosts() as $value_26) {
                $values_13[] = $value_26;
            }
            $value_25 = $values_13;
        }
        if (is_null($object->getExtraHosts())) {
            $value_25 = $object->getExtraHosts();
        }
        $data->{'ExtraHosts'} = $value_25;
        $value_27             = $object->getVolumesFrom();
        if (is_array($object->getVolumesFrom())) {
            $values_14 = [];
            foreach ($object->getVolumesFrom() as $value_28) {
                $values_14[] = $value_28;
            }
            $value_27 = $values_14;
        }
        if (is_null($object->getVolumesFrom())) {
            $value_27 = $object->getVolumesFrom();
        }
        $data->{'VolumesFrom'} = $value_27;
        $value_29              = $object->getCapAdd();
        if (is_array($object->getCapAdd())) {
            $values_15 = [];
            foreach ($object->getCapAdd() as $value_30) {
                $values_15[] = $value_30;
            }
            $value_29 = $values_15;
        }
        if (is_null($object->getCapAdd())) {
            $value_29 = $object->getCapAdd();
        }
        $data->{'CapAdd'} = $value_29;
        $value_31         = $object->getCapDrop();
        if (is_array($object->getCapDrop())) {
            $values_16 = [];
            foreach ($object->getCapDrop() as $value_32) {
                $values_16[] = $value_32;
            }
            $value_31 = $values_16;
        }
        if (is_null($object->getCapDrop())) {
            $value_31 = $object->getCapDrop();
        }
        $data->{'CapDrop'} = $value_31;
        $value_33          = $object->getGroupAdd();
        if (is_array($object->getGroupAdd())) {
            $values_17 = [];
            foreach ($object->getGroupAdd() as $value_34) {
                $values_17[] = $value_34;
            }
            $value_33 = $values_17;
        }
        if (is_null($object->getGroupAdd())) {
            $value_33 = $object->getGroupAdd();
        }
        $data->{'GroupAdd'} = $value_33;
        if (null !== $object->getRestartPolicy()) {
            $data->{'RestartPolicy'} = $this->serializer->serialize($object->getRestartPolicy(), 'raw', $context);
        }
        if (null !== $object->getUsernsMode()) {
            $data->{'UsernsMode'} = $object->getUsernsMode();
        }
        if (null !== $object->getNetworkMode()) {
            $data->{'NetworkMode'} = $object->getNetworkMode();
        }
        $value_35 = $object->getDevices();
        if (is_array($object->getDevices())) {
            $values_18 = [];
            foreach ($object->getDevices() as $value_36) {
                $values_18[] = $this->serializer->serialize($value_36, 'raw', $context);
            }
            $value_35 = $values_18;
        }
        if (is_null($object->getDevices())) {
            $value_35 = $object->getDevices();
        }
        $data->{'Devices'} = $value_35;
        $value_37          = $object->getUlimits();
        if (is_array($object->getUlimits())) {
            $values_19 = [];
            foreach ($object->getUlimits() as $value_38) {
                $values_19[] = $this->serializer->serialize($value_38, 'raw', $context);
            }
            $value_37 = $values_19;
        }
        if (is_null($object->getUlimits())) {
            $value_37 = $object->getUlimits();
        }
        $data->{'Ulimits'} = $value_37;
        $value_39          = $object->getSecurityOpt();
        if (is_array($object->getSecurityOpt())) {
            $values_20 = [];
            foreach ($object->getSecurityOpt() as $value_40) {
                $values_20[] = $value_40;
            }
            $value_39 = $values_20;
        }
        if (is_null($object->getSecurityOpt())) {
            $value_39 = $object->getSecurityOpt();
        }
        $data->{'SecurityOpt'} = $value_39;
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
