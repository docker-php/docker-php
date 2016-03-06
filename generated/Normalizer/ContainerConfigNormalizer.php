<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ContainerConfigNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ContainerConfig') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ContainerConfig) {
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
        $object = new \Docker\API\Model\ContainerConfig();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Id'})) {
            $object->setId($data->{'Id'});
        }
        if (isset($data->{'Names'})) {
            $values_81 = [];
            foreach ($data->{'Names'} as $value_82) {
                $values_81[] = $value_82;
            }
            $object->setNames($values_81);
        }
        if (isset($data->{'Image'})) {
            $object->setImage($data->{'Image'});
        }
        if (isset($data->{'ImageID'})) {
            $object->setImageID($data->{'ImageID'});
        }
        if (isset($data->{'Command'})) {
            $object->setCommand($data->{'Command'});
        }
        if (isset($data->{'Created'})) {
            $object->setCreated($data->{'Created'});
        }
        if (isset($data->{'Status'})) {
            $object->setStatus($data->{'Status'});
        }
        if (isset($data->{'Ports'})) {
            $values_83 = [];
            foreach ($data->{'Ports'} as $value_84) {
                $values_83[] = $this->serializer->deserialize($value_84, 'Docker\\API\\Model\\Port', 'raw', $context);
            }
            $object->setPorts($values_83);
        }
        if (isset($data->{'Labels'})) {
            $values_85 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Labels'} as $key_87 => $value_86) {
                $values_85[$key_87] = $value_86;
            }
            $object->setLabels($values_85);
        }
        if (isset($data->{'SizeRw'})) {
            $object->setSizeRw($data->{'SizeRw'});
        }
        if (isset($data->{'SizeRootFs'})) {
            $object->setSizeRootFs($data->{'SizeRootFs'});
        }
        if (isset($data->{'Hostname'})) {
            $object->setHostname($data->{'Hostname'});
        }
        if (isset($data->{'Domainname'})) {
            $object->setDomainname($data->{'Domainname'});
        }
        if (isset($data->{'User'})) {
            $object->setUser($data->{'User'});
        }
        if (isset($data->{'AttachStdin'})) {
            $object->setAttachStdin($data->{'AttachStdin'});
        }
        if (isset($data->{'AttachStdout'})) {
            $object->setAttachStdout($data->{'AttachStdout'});
        }
        if (isset($data->{'AttachStderr'})) {
            $object->setAttachStderr($data->{'AttachStderr'});
        }
        if (isset($data->{'Tty'})) {
            $object->setTty($data->{'Tty'});
        }
        if (isset($data->{'OpenStdin'})) {
            $object->setOpenStdin($data->{'OpenStdin'});
        }
        if (isset($data->{'StdinOnce'})) {
            $object->setStdinOnce($data->{'StdinOnce'});
        }
        if (isset($data->{'Env'})) {
            $values_88 = [];
            foreach ($data->{'Env'} as $value_89) {
                $values_88[] = $value_89;
            }
            $object->setEnv($values_88);
        }
        if (isset($data->{'Cmd'})) {
            $value_90 = $data->{'Cmd'};
            if (is_array($data->{'Cmd'})) {
                $values_91 = [];
                foreach ($data->{'Cmd'} as $value_92) {
                    $values_91[] = $value_92;
                }
                $value_90 = $values_91;
            }
            if (is_string($data->{'Cmd'})) {
                $value_90 = $data->{'Cmd'};
            }
            $object->setCmd($value_90);
        }
        if (isset($data->{'Entrypoint'})) {
            $value_93 = $data->{'Entrypoint'};
            if (is_array($data->{'Entrypoint'})) {
                $values_94 = [];
                foreach ($data->{'Entrypoint'} as $value_95) {
                    $values_94[] = $value_95;
                }
                $value_93 = $values_94;
            }
            if (is_string($data->{'Entrypoint'})) {
                $value_93 = $data->{'Entrypoint'};
            }
            $object->setEntrypoint($value_93);
        }
        if (isset($data->{'Mounts'})) {
            $values_96 = [];
            foreach ($data->{'Mounts'} as $value_97) {
                $values_96[] = $this->serializer->deserialize($value_97, 'Docker\\API\\Model\\Mount', 'raw', $context);
            }
            $object->setMounts($values_96);
        }
        if (isset($data->{'WorkingDir'})) {
            $object->setWorkingDir($data->{'WorkingDir'});
        }
        if (isset($data->{'NetworkDisabled'})) {
            $object->setNetworkDisabled($data->{'NetworkDisabled'});
        }
        if (isset($data->{'MacAddress'})) {
            $object->setMacAddress($data->{'MacAddress'});
        }
        if (isset($data->{'ExposedPorts'})) {
            $values_98 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'ExposedPorts'} as $key_100 => $value_99) {
                $values_98[$key_100] = $value_99;
            }
            $object->setExposedPorts($values_98);
        }
        if (isset($data->{'NetworkSettings'})) {
            $object->setNetworkSettings($this->serializer->deserialize($data->{'NetworkSettings'}, 'Docker\\API\\Model\\NetworkConfig', 'raw', $context));
        }
        if (isset($data->{'HostConfig'})) {
            $object->setHostConfig($this->serializer->deserialize($data->{'HostConfig'}, 'Docker\\API\\Model\\HostConfig', 'raw', $context));
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getId()) {
            $data->{'Id'} = $object->getId();
        }
        if (null !== $object->getNames()) {
            $values_101 = [];
            foreach ($object->getNames() as $value_102) {
                $values_101[] = $value_102;
            }
            $data->{'Names'} = $values_101;
        }
        if (null !== $object->getImage()) {
            $data->{'Image'} = $object->getImage();
        }
        if (null !== $object->getImageID()) {
            $data->{'ImageID'} = $object->getImageID();
        }
        if (null !== $object->getCommand()) {
            $data->{'Command'} = $object->getCommand();
        }
        if (null !== $object->getCreated()) {
            $data->{'Created'} = $object->getCreated();
        }
        if (null !== $object->getStatus()) {
            $data->{'Status'} = $object->getStatus();
        }
        if (null !== $object->getPorts()) {
            $values_103 = [];
            foreach ($object->getPorts() as $value_104) {
                $values_103[] = $this->serializer->serialize($value_104, 'raw', $context);
            }
            $data->{'Ports'} = $values_103;
        }
        if (null !== $object->getLabels()) {
            $values_105 = new \stdClass();
            foreach ($object->getLabels() as $key_107 => $value_106) {
                $values_105->{$key_107} = $value_106;
            }
            $data->{'Labels'} = $values_105;
        }
        if (null !== $object->getSizeRw()) {
            $data->{'SizeRw'} = $object->getSizeRw();
        }
        if (null !== $object->getSizeRootFs()) {
            $data->{'SizeRootFs'} = $object->getSizeRootFs();
        }
        if (null !== $object->getHostname()) {
            $data->{'Hostname'} = $object->getHostname();
        }
        if (null !== $object->getDomainname()) {
            $data->{'Domainname'} = $object->getDomainname();
        }
        if (null !== $object->getUser()) {
            $data->{'User'} = $object->getUser();
        }
        if (null !== $object->getAttachStdin()) {
            $data->{'AttachStdin'} = $object->getAttachStdin();
        }
        if (null !== $object->getAttachStdout()) {
            $data->{'AttachStdout'} = $object->getAttachStdout();
        }
        if (null !== $object->getAttachStderr()) {
            $data->{'AttachStderr'} = $object->getAttachStderr();
        }
        if (null !== $object->getTty()) {
            $data->{'Tty'} = $object->getTty();
        }
        if (null !== $object->getOpenStdin()) {
            $data->{'OpenStdin'} = $object->getOpenStdin();
        }
        if (null !== $object->getStdinOnce()) {
            $data->{'StdinOnce'} = $object->getStdinOnce();
        }
        if (null !== $object->getEnv()) {
            $values_108 = [];
            foreach ($object->getEnv() as $value_109) {
                $values_108[] = $value_109;
            }
            $data->{'Env'} = $values_108;
        }
        if (null !== $object->getCmd()) {
            $value_110 = $object->getCmd();
            if (is_array($object->getCmd())) {
                $values_111 = [];
                foreach ($object->getCmd() as $value_112) {
                    $values_111[] = $value_112;
                }
                $value_110 = $values_111;
            }
            if (is_string($object->getCmd())) {
                $value_110 = $object->getCmd();
            }
            $data->{'Cmd'} = $value_110;
        }
        if (null !== $object->getEntrypoint()) {
            $value_113 = $object->getEntrypoint();
            if (is_array($object->getEntrypoint())) {
                $values_114 = [];
                foreach ($object->getEntrypoint() as $value_115) {
                    $values_114[] = $value_115;
                }
                $value_113 = $values_114;
            }
            if (is_string($object->getEntrypoint())) {
                $value_113 = $object->getEntrypoint();
            }
            $data->{'Entrypoint'} = $value_113;
        }
        if (null !== $object->getMounts()) {
            $values_116 = [];
            foreach ($object->getMounts() as $value_117) {
                $values_116[] = $this->serializer->serialize($value_117, 'raw', $context);
            }
            $data->{'Mounts'} = $values_116;
        }
        if (null !== $object->getWorkingDir()) {
            $data->{'WorkingDir'} = $object->getWorkingDir();
        }
        if (null !== $object->getNetworkDisabled()) {
            $data->{'NetworkDisabled'} = $object->getNetworkDisabled();
        }
        if (null !== $object->getMacAddress()) {
            $data->{'MacAddress'} = $object->getMacAddress();
        }
        if (null !== $object->getExposedPorts()) {
            $values_118 = new \stdClass();
            foreach ($object->getExposedPorts() as $key_120 => $value_119) {
                $values_118->{$key_120} = $value_119;
            }
            $data->{'ExposedPorts'} = $values_118;
        }
        if (null !== $object->getNetworkSettings()) {
            $data->{'NetworkSettings'} = $this->serializer->serialize($object->getNetworkSettings(), 'raw', $context);
        }
        if (null !== $object->getHostConfig()) {
            $data->{'HostConfig'} = $this->serializer->serialize($object->getHostConfig(), 'raw', $context);
        }

        return $data;
    }
}
