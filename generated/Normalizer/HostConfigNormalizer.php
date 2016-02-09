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
        if (isset($data->{'Binds'})) {
            $values_3 = [];
            foreach ($data->{'Binds'} as $value_4) {
                $values_3[] = $value_4;
            }
            $object->setBinds($values_3);
        }
        if (isset($data->{'Links'})) {
            $values_5 = [];
            foreach ($data->{'Links'} as $value_6) {
                $values_5[] = $value_6;
            }
            $object->setLinks($values_5);
        }
        if (isset($data->{'LxcConf'})) {
            $values_7 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'LxcConf'} as $key_9 => $value_8) {
                $values_7[$key_9] = $value_8;
            }
            $object->setLxcConf($values_7);
        }
        if (isset($data->{'Memory'})) {
            $object->setMemory($data->{'Memory'});
        }
        if (isset($data->{'MemorySwap'})) {
            $object->setMemorySwap($data->{'MemorySwap'});
        }
        if (isset($data->{'CpuShares'})) {
            $object->setCpuShares($data->{'CpuShares'});
        }
        if (isset($data->{'CpuPeriod'})) {
            $object->setCpuPeriod($data->{'CpuPeriod'});
        }
        if (isset($data->{'CpusetCpus'})) {
            $object->setCpusetCpus($data->{'CpusetCpus'});
        }
        if (isset($data->{'CpusetMems'})) {
            $object->setCpusetMems($data->{'CpusetMems'});
        }
        if (isset($data->{'BlkioWeight'})) {
            $object->setBlkioWeight($data->{'BlkioWeight'});
        }
        if (isset($data->{'BlkioWeightDevice'})) {
            $values_10 = [];
            foreach ($data->{'BlkioWeightDevice'} as $value_11) {
                $values_10[] = $this->serializer->deserialize($value_11, 'Docker\\API\\Model\\DeviceWeight', 'raw', $context);
            }
            $object->setBlkioWeightDevice($values_10);
        }
        if (isset($data->{'BlkioDeviceReadBps'})) {
            $values_12 = [];
            foreach ($data->{'BlkioDeviceReadBps'} as $value_13) {
                $values_12[] = $this->serializer->deserialize($value_13, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
            }
            $object->setBlkioDeviceReadBps($values_12);
        }
        if (isset($data->{'BlkioDeviceReadIOps'})) {
            $values_14 = [];
            foreach ($data->{'BlkioDeviceReadIOps'} as $value_15) {
                $values_14[] = $this->serializer->deserialize($value_15, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
            }
            $object->setBlkioDeviceReadIOps($values_14);
        }
        if (isset($data->{'BlkioDeviceWriteBps'})) {
            $values_16 = [];
            foreach ($data->{'BlkioDeviceWriteBps'} as $value_17) {
                $values_16[] = $this->serializer->deserialize($value_17, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
            }
            $object->setBlkioDeviceWriteBps($values_16);
        }
        if (isset($data->{'BlkioDeviceWriteIOps'})) {
            $values_18 = [];
            foreach ($data->{'BlkioDeviceWriteIOps'} as $value_19) {
                $values_18[] = $this->serializer->deserialize($value_19, 'Docker\\API\\Model\\DeviceRate', 'raw', $context);
            }
            $object->setBlkioDeviceWriteIOps($values_18);
        }
        if (isset($data->{'MemorySwappiness'})) {
            $object->setMemorySwappiness($data->{'MemorySwappiness'});
        }
        if (isset($data->{'OomKillDisable'})) {
            $object->setOomKillDisable($data->{'OomKillDisable'});
        }
        if (isset($data->{'PortBindings'})) {
            $values_20 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'PortBindings'} as $key_22 => $value_21) {
                $values_20[$key_22] = $this->serializer->deserialize($value_21, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
            }
            $object->setPortBindings($values_20);
        }
        if (isset($data->{'PublishAllPorts'})) {
            $object->setPublishAllPorts($data->{'PublishAllPorts'});
        }
        if (isset($data->{'Privileged'})) {
            $object->setPrivileged($data->{'Privileged'});
        }
        if (isset($data->{'ReadonlyRootfs'})) {
            $object->setReadonlyRootfs($data->{'ReadonlyRootfs'});
        }
        if (isset($data->{'Dns'})) {
            $values_23 = [];
            foreach ($data->{'Dns'} as $value_24) {
                $values_23[] = $value_24;
            }
            $object->setDns($values_23);
        }
        if (isset($data->{'DnsSearch'})) {
            $values_25 = [];
            foreach ($data->{'DnsSearch'} as $value_26) {
                $values_25[] = $value_26;
            }
            $object->setDnsSearch($values_25);
        }
        if (isset($data->{'ExtraHosts'})) {
            $values_27 = [];
            foreach ($data->{'ExtraHosts'} as $value_28) {
                $values_27[] = $value_28;
            }
            $object->setExtraHosts($values_27);
        }
        if (isset($data->{'VolumesFrom'})) {
            $values_29 = [];
            foreach ($data->{'VolumesFrom'} as $value_30) {
                $values_29[] = $value_30;
            }
            $object->setVolumesFrom($values_29);
        }
        if (isset($data->{'CapAdd'})) {
            $values_31 = [];
            foreach ($data->{'CapAdd'} as $value_32) {
                $values_31[] = $value_32;
            }
            $object->setCapAdd($values_31);
        }
        if (isset($data->{'CapDrop'})) {
            $values_33 = [];
            foreach ($data->{'CapDrop'} as $value_34) {
                $values_33[] = $value_34;
            }
            $object->setCapDrop($values_33);
        }
        if (isset($data->{'RestartPolicy'})) {
            $object->setRestartPolicy($this->serializer->deserialize($data->{'RestartPolicy'}, 'Docker\\API\\Model\\RestartPolicy', 'raw', $context));
        }
        if (isset($data->{'NetworkMode'})) {
            $object->setNetworkMode($data->{'NetworkMode'});
        }
        if (isset($data->{'Devices'})) {
            $values_35 = [];
            foreach ($data->{'Devices'} as $value_36) {
                $values_35[] = $this->serializer->deserialize($value_36, 'Docker\\API\\Model\\Device', 'raw', $context);
            }
            $object->setDevices($values_35);
        }
        if (isset($data->{'Ulimits'})) {
            $values_37 = [];
            foreach ($data->{'Ulimits'} as $value_38) {
                $values_37[] = $this->serializer->deserialize($value_38, 'Docker\\API\\Model\\Ulimit', 'raw', $context);
            }
            $object->setUlimits($values_37);
        }
        if (isset($data->{'SecurityOpt'})) {
            $values_39 = [];
            foreach ($data->{'SecurityOpt'} as $value_40) {
                $values_39[] = $value_40;
            }
            $object->setSecurityOpt($values_39);
        }
        if (isset($data->{'LogConfig'})) {
            $object->setLogConfig($this->serializer->deserialize($data->{'LogConfig'}, 'Docker\\API\\Model\\LogConfig', 'raw', $context));
        }
        if (isset($data->{'CgroupParent'})) {
            $object->setCgroupParent($data->{'CgroupParent'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getBinds()) {
            $values_41 = [];
            foreach ($object->getBinds() as $value_42) {
                $values_41[] = $value_42;
            }
            $data->{'Binds'} = $values_41;
        }
        if (null !== $object->getLinks()) {
            $values_43 = [];
            foreach ($object->getLinks() as $value_44) {
                $values_43[] = $value_44;
            }
            $data->{'Links'} = $values_43;
        }
        if (null !== $object->getLxcConf()) {
            $values_45 = new \stdClass();
            foreach ($object->getLxcConf() as $key_47 => $value_46) {
                $values_45->{$key_47} = $value_46;
            }
            $data->{'LxcConf'} = $values_45;
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
            $values_48 = [];
            foreach ($object->getBlkioWeightDevice() as $value_49) {
                $values_48[] = $this->serializer->serialize($value_49, 'raw', $context);
            }
            $data->{'BlkioWeightDevice'} = $values_48;
        }
        if (null !== $object->getBlkioDeviceReadBps()) {
            $values_50 = [];
            foreach ($object->getBlkioDeviceReadBps() as $value_51) {
                $values_50[] = $this->serializer->serialize($value_51, 'raw', $context);
            }
            $data->{'BlkioDeviceReadBps'} = $values_50;
        }
        if (null !== $object->getBlkioDeviceReadIOps()) {
            $values_52 = [];
            foreach ($object->getBlkioDeviceReadIOps() as $value_53) {
                $values_52[] = $this->serializer->serialize($value_53, 'raw', $context);
            }
            $data->{'BlkioDeviceReadIOps'} = $values_52;
        }
        if (null !== $object->getBlkioDeviceWriteBps()) {
            $values_54 = [];
            foreach ($object->getBlkioDeviceWriteBps() as $value_55) {
                $values_54[] = $this->serializer->serialize($value_55, 'raw', $context);
            }
            $data->{'BlkioDeviceWriteBps'} = $values_54;
        }
        if (null !== $object->getBlkioDeviceWriteIOps()) {
            $values_56 = [];
            foreach ($object->getBlkioDeviceWriteIOps() as $value_57) {
                $values_56[] = $this->serializer->serialize($value_57, 'raw', $context);
            }
            $data->{'BlkioDeviceWriteIOps'} = $values_56;
        }
        if (null !== $object->getMemorySwappiness()) {
            $data->{'MemorySwappiness'} = $object->getMemorySwappiness();
        }
        if (null !== $object->getOomKillDisable()) {
            $data->{'OomKillDisable'} = $object->getOomKillDisable();
        }
        if (null !== $object->getPortBindings()) {
            $values_58 = new \stdClass();
            foreach ($object->getPortBindings() as $key_60 => $value_59) {
                $values_58->{$key_60} = $this->serializer->serialize($value_59, 'raw', $context);
            }
            $data->{'PortBindings'} = $values_58;
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
            $values_61 = [];
            foreach ($object->getDns() as $value_62) {
                $values_61[] = $value_62;
            }
            $data->{'Dns'} = $values_61;
        }
        if (null !== $object->getDnsSearch()) {
            $values_63 = [];
            foreach ($object->getDnsSearch() as $value_64) {
                $values_63[] = $value_64;
            }
            $data->{'DnsSearch'} = $values_63;
        }
        if (null !== $object->getExtraHosts()) {
            $values_65 = [];
            foreach ($object->getExtraHosts() as $value_66) {
                $values_65[] = $value_66;
            }
            $data->{'ExtraHosts'} = $values_65;
        }
        if (null !== $object->getVolumesFrom()) {
            $values_67 = [];
            foreach ($object->getVolumesFrom() as $value_68) {
                $values_67[] = $value_68;
            }
            $data->{'VolumesFrom'} = $values_67;
        }
        if (null !== $object->getCapAdd()) {
            $values_69 = [];
            foreach ($object->getCapAdd() as $value_70) {
                $values_69[] = $value_70;
            }
            $data->{'CapAdd'} = $values_69;
        }
        if (null !== $object->getCapDrop()) {
            $values_71 = [];
            foreach ($object->getCapDrop() as $value_72) {
                $values_71[] = $value_72;
            }
            $data->{'CapDrop'} = $values_71;
        }
        if (null !== $object->getRestartPolicy()) {
            $data->{'RestartPolicy'} = $this->serializer->serialize($object->getRestartPolicy(), 'raw', $context);
        }
        if (null !== $object->getNetworkMode()) {
            $data->{'NetworkMode'} = $object->getNetworkMode();
        }
        if (null !== $object->getDevices()) {
            $values_73 = [];
            foreach ($object->getDevices() as $value_74) {
                $values_73[] = $this->serializer->serialize($value_74, 'raw', $context);
            }
            $data->{'Devices'} = $values_73;
        }
        if (null !== $object->getUlimits()) {
            $values_75 = [];
            foreach ($object->getUlimits() as $value_76) {
                $values_75[] = $this->serializer->serialize($value_76, 'raw', $context);
            }
            $data->{'Ulimits'} = $values_75;
        }
        if (null !== $object->getSecurityOpt()) {
            $values_77 = [];
            foreach ($object->getSecurityOpt() as $value_78) {
                $values_77[] = $value_78;
            }
            $data->{'SecurityOpt'} = $values_77;
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
