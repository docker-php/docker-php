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
        if (property_exists($data, 'MemorySwappiness')) {
            $object->setMemorySwappiness($data->{'MemorySwappiness'});
        }
        if (property_exists($data, 'OomKillDisable')) {
            $object->setOomKillDisable($data->{'OomKillDisable'});
        }
        if (property_exists($data, 'PortBindings')) {
            $values_3 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'PortBindings'} as $key_1 => $value_5) {
                $values_3[$key_1] = $this->serializer->deserialize($value_5, 'Docker\\API\\Model\\PortBinding', 'raw', $context);
            }
            $object->setPortBindings($values_3);
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
            $value_6 = $data->{'Dns'};
            if (is_array($data->{'Dns'})) {
                $values_4 = [];
                foreach ($data->{'Dns'} as $value_7) {
                    $values_4[] = $value_7;
                }
                $value_6 = $values_4;
            }
            if (is_null($data->{'Dns'})) {
                $value_6 = $data->{'Dns'};
            }
            $object->setDns($value_6);
        }
        if (property_exists($data, 'DnsSearch')) {
            $value_8 = $data->{'DnsSearch'};
            if (is_array($data->{'DnsSearch'})) {
                $values_5 = [];
                foreach ($data->{'DnsSearch'} as $value_9) {
                    $values_5[] = $value_9;
                }
                $value_8 = $values_5;
            }
            if (is_null($data->{'DnsSearch'})) {
                $value_8 = $data->{'DnsSearch'};
            }
            $object->setDnsSearch($value_8);
        }
        if (property_exists($data, 'ExtraHosts')) {
            $value_10 = $data->{'ExtraHosts'};
            if (is_array($data->{'ExtraHosts'})) {
                $values_6 = [];
                foreach ($data->{'ExtraHosts'} as $value_11) {
                    $values_6[] = $value_11;
                }
                $value_10 = $values_6;
            }
            if (is_null($data->{'ExtraHosts'})) {
                $value_10 = $data->{'ExtraHosts'};
            }
            $object->setExtraHosts($value_10);
        }
        if (property_exists($data, 'VolumesFrom')) {
            $value_12 = $data->{'VolumesFrom'};
            if (is_array($data->{'VolumesFrom'})) {
                $values_7 = [];
                foreach ($data->{'VolumesFrom'} as $value_13) {
                    $values_7[] = $value_13;
                }
                $value_12 = $values_7;
            }
            if (is_null($data->{'VolumesFrom'})) {
                $value_12 = $data->{'VolumesFrom'};
            }
            $object->setVolumesFrom($value_12);
        }
        if (property_exists($data, 'CapAdd')) {
            $value_14 = $data->{'CapAdd'};
            if (is_array($data->{'CapAdd'})) {
                $values_8 = [];
                foreach ($data->{'CapAdd'} as $value_15) {
                    $values_8[] = $value_15;
                }
                $value_14 = $values_8;
            }
            if (is_null($data->{'CapAdd'})) {
                $value_14 = $data->{'CapAdd'};
            }
            $object->setCapAdd($value_14);
        }
        if (property_exists($data, 'CapDrop')) {
            $value_16 = $data->{'CapDrop'};
            if (is_array($data->{'CapDrop'})) {
                $values_9 = [];
                foreach ($data->{'CapDrop'} as $value_17) {
                    $values_9[] = $value_17;
                }
                $value_16 = $values_9;
            }
            if (is_null($data->{'CapDrop'})) {
                $value_16 = $data->{'CapDrop'};
            }
            $object->setCapDrop($value_16);
        }
        if (property_exists($data, 'RestartPolicy')) {
            $object->setRestartPolicy($this->serializer->deserialize($data->{'RestartPolicy'}, 'Docker\\API\\Model\\RestartPolicy', 'raw', $context));
        }
        if (property_exists($data, 'NetworkMode')) {
            $object->setNetworkMode($data->{'NetworkMode'});
        }
        if (property_exists($data, 'Devices')) {
            $value_18 = $data->{'Devices'};
            if (is_array($data->{'Devices'})) {
                $values_10 = [];
                foreach ($data->{'Devices'} as $value_19) {
                    $values_10[] = $this->serializer->deserialize($value_19, 'Docker\\API\\Model\\Device', 'raw', $context);
                }
                $value_18 = $values_10;
            }
            if (is_null($data->{'Devices'})) {
                $value_18 = $data->{'Devices'};
            }
            $object->setDevices($value_18);
        }
        if (property_exists($data, 'Ulimits')) {
            $value_20 = $data->{'Ulimits'};
            if (is_array($data->{'Ulimits'})) {
                $values_11 = [];
                foreach ($data->{'Ulimits'} as $value_21) {
                    $values_11[] = $this->serializer->deserialize($value_21, 'Docker\\API\\Model\\Ulimit', 'raw', $context);
                }
                $value_20 = $values_11;
            }
            if (is_null($data->{'Ulimits'})) {
                $value_20 = $data->{'Ulimits'};
            }
            $object->setUlimits($value_20);
        }
        if (property_exists($data, 'SecurityOpt')) {
            $value_22 = $data->{'SecurityOpt'};
            if (is_array($data->{'SecurityOpt'})) {
                $values_12 = [];
                foreach ($data->{'SecurityOpt'} as $value_23) {
                    $values_12[] = $value_23;
                }
                $value_22 = $values_12;
            }
            if (is_null($data->{'SecurityOpt'})) {
                $value_22 = $data->{'SecurityOpt'};
            }
            $object->setSecurityOpt($value_22);
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
        if (null !== $object->getMemorySwappiness()) {
            $data->{'MemorySwappiness'} = $object->getMemorySwappiness();
        }
        if (null !== $object->getOomKillDisable()) {
            $data->{'OomKillDisable'} = $object->getOomKillDisable();
        }
        if (null !== $object->getPortBindings()) {
            $values_3 = new \stdClass();
            foreach ($object->getPortBindings() as $key_1 => $value_5) {
                $values_3->{$key_1} = $this->serializer->serialize($value_5, 'raw', $context);
            }
            $data->{'PortBindings'} = $values_3;
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
        $value_6 = $object->getDns();
        if (is_array($object->getDns())) {
            $values_4 = [];
            foreach ($object->getDns() as $value_7) {
                $values_4[] = $value_7;
            }
            $value_6 = $values_4;
        }
        if (is_null($object->getDns())) {
            $value_6 = $object->getDns();
        }
        $data->{'Dns'} = $value_6;
        $value_8       = $object->getDnsSearch();
        if (is_array($object->getDnsSearch())) {
            $values_5 = [];
            foreach ($object->getDnsSearch() as $value_9) {
                $values_5[] = $value_9;
            }
            $value_8 = $values_5;
        }
        if (is_null($object->getDnsSearch())) {
            $value_8 = $object->getDnsSearch();
        }
        $data->{'DnsSearch'} = $value_8;
        $value_10            = $object->getExtraHosts();
        if (is_array($object->getExtraHosts())) {
            $values_6 = [];
            foreach ($object->getExtraHosts() as $value_11) {
                $values_6[] = $value_11;
            }
            $value_10 = $values_6;
        }
        if (is_null($object->getExtraHosts())) {
            $value_10 = $object->getExtraHosts();
        }
        $data->{'ExtraHosts'} = $value_10;
        $value_12             = $object->getVolumesFrom();
        if (is_array($object->getVolumesFrom())) {
            $values_7 = [];
            foreach ($object->getVolumesFrom() as $value_13) {
                $values_7[] = $value_13;
            }
            $value_12 = $values_7;
        }
        if (is_null($object->getVolumesFrom())) {
            $value_12 = $object->getVolumesFrom();
        }
        $data->{'VolumesFrom'} = $value_12;
        $value_14              = $object->getCapAdd();
        if (is_array($object->getCapAdd())) {
            $values_8 = [];
            foreach ($object->getCapAdd() as $value_15) {
                $values_8[] = $value_15;
            }
            $value_14 = $values_8;
        }
        if (is_null($object->getCapAdd())) {
            $value_14 = $object->getCapAdd();
        }
        $data->{'CapAdd'} = $value_14;
        $value_16         = $object->getCapDrop();
        if (is_array($object->getCapDrop())) {
            $values_9 = [];
            foreach ($object->getCapDrop() as $value_17) {
                $values_9[] = $value_17;
            }
            $value_16 = $values_9;
        }
        if (is_null($object->getCapDrop())) {
            $value_16 = $object->getCapDrop();
        }
        $data->{'CapDrop'} = $value_16;
        if (null !== $object->getRestartPolicy()) {
            $data->{'RestartPolicy'} = $this->serializer->serialize($object->getRestartPolicy(), 'raw', $context);
        }
        if (null !== $object->getNetworkMode()) {
            $data->{'NetworkMode'} = $object->getNetworkMode();
        }
        $value_18 = $object->getDevices();
        if (is_array($object->getDevices())) {
            $values_10 = [];
            foreach ($object->getDevices() as $value_19) {
                $values_10[] = $this->serializer->serialize($value_19, 'raw', $context);
            }
            $value_18 = $values_10;
        }
        if (is_null($object->getDevices())) {
            $value_18 = $object->getDevices();
        }
        $data->{'Devices'} = $value_18;
        $value_20          = $object->getUlimits();
        if (is_array($object->getUlimits())) {
            $values_11 = [];
            foreach ($object->getUlimits() as $value_21) {
                $values_11[] = $this->serializer->serialize($value_21, 'raw', $context);
            }
            $value_20 = $values_11;
        }
        if (is_null($object->getUlimits())) {
            $value_20 = $object->getUlimits();
        }
        $data->{'Ulimits'} = $value_20;
        $value_22          = $object->getSecurityOpt();
        if (is_array($object->getSecurityOpt())) {
            $values_12 = [];
            foreach ($object->getSecurityOpt() as $value_23) {
                $values_12[] = $value_23;
            }
            $value_22 = $values_12;
        }
        if (is_null($object->getSecurityOpt())) {
            $value_22 = $object->getSecurityOpt();
        }
        $data->{'SecurityOpt'} = $value_22;
        if (null !== $object->getLogConfig()) {
            $data->{'LogConfig'} = $this->serializer->serialize($object->getLogConfig(), 'raw', $context);
        }
        if (null !== $object->getCgroupParent()) {
            $data->{'CgroupParent'} = $object->getCgroupParent();
        }

        return $data;
    }
}
