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
        if (isset($data->{'MemorySwappiness'})) {
            $object->setMemorySwappiness($data->{'MemorySwappiness'});
        }
        if (isset($data->{'OomKillDisable'})) {
            $object->setOomKillDisable($data->{'OomKillDisable'});
        }
        if (isset($data->{'PortBindings'})) {
            $values_10 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'PortBindings'} as $key_12 => $value_11) {
                $values_10[$key_12] = $this->serializer->deserialize($value_11, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
            }
            $object->setPortBindings($values_10);
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
            $values_13 = [];
            foreach ($data->{'Dns'} as $value_14) {
                $values_13[] = $value_14;
            }
            $object->setDns($values_13);
        }
        if (isset($data->{'DnsSearch'})) {
            $values_15 = [];
            foreach ($data->{'DnsSearch'} as $value_16) {
                $values_15[] = $value_16;
            }
            $object->setDnsSearch($values_15);
        }
        if (isset($data->{'ExtraHosts'})) {
            $values_17 = [];
            foreach ($data->{'ExtraHosts'} as $value_18) {
                $values_17[] = $value_18;
            }
            $object->setExtraHosts($values_17);
        }
        if (isset($data->{'VolumesFrom'})) {
            $values_19 = [];
            foreach ($data->{'VolumesFrom'} as $value_20) {
                $values_19[] = $value_20;
            }
            $object->setVolumesFrom($values_19);
        }
        if (isset($data->{'CapAdd'})) {
            $values_21 = [];
            foreach ($data->{'CapAdd'} as $value_22) {
                $values_21[] = $value_22;
            }
            $object->setCapAdd($values_21);
        }
        if (isset($data->{'CapDrop'})) {
            $values_23 = [];
            foreach ($data->{'CapDrop'} as $value_24) {
                $values_23[] = $value_24;
            }
            $object->setCapDrop($values_23);
        }
        if (isset($data->{'RestartPolicy'})) {
            $object->setRestartPolicy($this->serializer->deserialize($data->{'RestartPolicy'}, 'Docker\\API\\Model\\RestartPolicy', 'raw', $context));
        }
        if (isset($data->{'NetworkMode'})) {
            $object->setNetworkMode($data->{'NetworkMode'});
        }
        if (isset($data->{'Devices'})) {
            $values_25 = [];
            foreach ($data->{'Devices'} as $value_26) {
                $values_25[] = $this->serializer->deserialize($value_26, 'Docker\\API\\Model\\Device', 'raw', $context);
            }
            $object->setDevices($values_25);
        }
        if (isset($data->{'Ulimits'})) {
            $values_27 = [];
            foreach ($data->{'Ulimits'} as $value_28) {
                $values_27[] = $this->serializer->deserialize($value_28, 'Docker\\API\\Model\\Ulimit', 'raw', $context);
            }
            $object->setUlimits($values_27);
        }
        if (isset($data->{'SecurityOpt'})) {
            $values_29 = [];
            foreach ($data->{'SecurityOpt'} as $value_30) {
                $values_29[] = $value_30;
            }
            $object->setSecurityOpt($values_29);
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
            $values_31 = [];
            foreach ($object->getBinds() as $value_32) {
                $values_31[] = $value_32;
            }
            $data->{'Binds'} = $values_31;
        }
        if (null !== $object->getLinks()) {
            $values_33 = [];
            foreach ($object->getLinks() as $value_34) {
                $values_33[] = $value_34;
            }
            $data->{'Links'} = $values_33;
        }
        if (null !== $object->getLxcConf()) {
            $values_35 = new \stdClass();
            foreach ($object->getLxcConf() as $key_37 => $value_36) {
                $values_35->{$key_37} = $value_36;
            }
            $data->{'LxcConf'} = $values_35;
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
        if (null !== $object->getMemorySwappiness()) {
            $data->{'MemorySwappiness'} = $object->getMemorySwappiness();
        }
        if (null !== $object->getOomKillDisable()) {
            $data->{'OomKillDisable'} = $object->getOomKillDisable();
        }
        if (null !== $object->getPortBindings()) {
            $values_38 = new \stdClass();
            foreach ($object->getPortBindings() as $key_40 => $value_39) {
                $values_38->{$key_40} = $this->serializer->serialize($value_39, 'raw', $context);
            }
            $data->{'PortBindings'} = $values_38;
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
            $values_41 = [];
            foreach ($object->getDns() as $value_42) {
                $values_41[] = $value_42;
            }
            $data->{'Dns'} = $values_41;
        }
        if (null !== $object->getDnsSearch()) {
            $values_43 = [];
            foreach ($object->getDnsSearch() as $value_44) {
                $values_43[] = $value_44;
            }
            $data->{'DnsSearch'} = $values_43;
        }
        if (null !== $object->getExtraHosts()) {
            $values_45 = [];
            foreach ($object->getExtraHosts() as $value_46) {
                $values_45[] = $value_46;
            }
            $data->{'ExtraHosts'} = $values_45;
        }
        if (null !== $object->getVolumesFrom()) {
            $values_47 = [];
            foreach ($object->getVolumesFrom() as $value_48) {
                $values_47[] = $value_48;
            }
            $data->{'VolumesFrom'} = $values_47;
        }
        if (null !== $object->getCapAdd()) {
            $values_49 = [];
            foreach ($object->getCapAdd() as $value_50) {
                $values_49[] = $value_50;
            }
            $data->{'CapAdd'} = $values_49;
        }
        if (null !== $object->getCapDrop()) {
            $values_51 = [];
            foreach ($object->getCapDrop() as $value_52) {
                $values_51[] = $value_52;
            }
            $data->{'CapDrop'} = $values_51;
        }
        if (null !== $object->getRestartPolicy()) {
            $data->{'RestartPolicy'} = $this->serializer->serialize($object->getRestartPolicy(), 'raw', $context);
        }
        if (null !== $object->getNetworkMode()) {
            $data->{'NetworkMode'} = $object->getNetworkMode();
        }
        if (null !== $object->getDevices()) {
            $values_53 = [];
            foreach ($object->getDevices() as $value_54) {
                $values_53[] = $this->serializer->serialize($value_54, 'raw', $context);
            }
            $data->{'Devices'} = $values_53;
        }
        if (null !== $object->getUlimits()) {
            $values_55 = [];
            foreach ($object->getUlimits() as $value_56) {
                $values_55[] = $this->serializer->serialize($value_56, 'raw', $context);
            }
            $data->{'Ulimits'} = $values_55;
        }
        if (null !== $object->getSecurityOpt()) {
            $values_57 = [];
            foreach ($object->getSecurityOpt() as $value_58) {
                $values_57[] = $value_58;
            }
            $data->{'SecurityOpt'} = $values_57;
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
