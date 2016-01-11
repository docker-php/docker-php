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
            $values_59 = [];
            foreach ($data->{'Names'} as $value_60) {
                $values_59[] = $value_60;
            }
            $object->setNames($values_59);
        }
        if (isset($data->{'Image'})) {
            $object->setImage($data->{'Image'});
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
            $values_61 = [];
            foreach ($data->{'Ports'} as $value_62) {
                $values_61[] = $this->serializer->deserialize($value_62, 'Docker\\API\\Model\\Port', 'raw', $context);
            }
            $object->setPorts($values_61);
        }
        if (isset($data->{'Labels'})) {
            $values_63 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'Labels'} as $key_65 => $value_64) {
                $values_63[$key_65] = $value_64;
            }
            $object->setLabels($values_63);
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
            $values_66 = [];
            foreach ($data->{'Env'} as $value_67) {
                $values_66[] = $value_67;
            }
            $object->setEnv($values_66);
        }
        if (isset($data->{'Cmd'})) {
            $value_68 = $data->{'Cmd'};
            if (is_array($data->{'Cmd'})) {
                $values_69 = [];
                foreach ($data->{'Cmd'} as $value_70) {
                    $values_69[] = $value_70;
                }
                $value_68 = $values_69;
            }
            if (is_string($data->{'Cmd'})) {
                $value_68 = $data->{'Cmd'};
            }
            $object->setCmd($value_68);
        }
        if (isset($data->{'Entrypoint'})) {
            $value_71 = $data->{'Entrypoint'};
            if (is_array($data->{'Entrypoint'})) {
                $values_72 = [];
                foreach ($data->{'Entrypoint'} as $value_73) {
                    $values_72[] = $value_73;
                }
                $value_71 = $values_72;
            }
            if (is_string($data->{'Entrypoint'})) {
                $value_71 = $data->{'Entrypoint'};
            }
            $object->setEntrypoint($value_71);
        }
        if (isset($data->{'Mounts'})) {
            $values_74 = [];
            foreach ($data->{'Mounts'} as $value_75) {
                $values_74[] = $this->serializer->deserialize($value_75, 'Docker\\API\\Model\\Mount', 'raw', $context);
            }
            $object->setMounts($values_74);
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
            $values_76 = new \ArrayObject([], \ArrayObject::ARRAY_AS_PROPS);
            foreach ($data->{'ExposedPorts'} as $key_78 => $value_77) {
                $values_76[$key_78] = $value_77;
            }
            $object->setExposedPorts($values_76);
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
            $values_79 = [];
            foreach ($object->getNames() as $value_80) {
                $values_79[] = $value_80;
            }
            $data->{'Names'} = $values_79;
        }
        if (null !== $object->getImage()) {
            $data->{'Image'} = $object->getImage();
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
            $values_81 = [];
            foreach ($object->getPorts() as $value_82) {
                $values_81[] = $this->serializer->serialize($value_82, 'raw', $context);
            }
            $data->{'Ports'} = $values_81;
        }
        if (null !== $object->getLabels()) {
            $values_83 = new \stdClass();
            foreach ($object->getLabels() as $key_85 => $value_84) {
                $values_83->{$key_85} = $value_84;
            }
            $data->{'Labels'} = $values_83;
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
            $values_86 = [];
            foreach ($object->getEnv() as $value_87) {
                $values_86[] = $value_87;
            }
            $data->{'Env'} = $values_86;
        }
        if (null !== $object->getCmd()) {
            $value_88 = $object->getCmd();
            if (is_array($object->getCmd())) {
                $values_89 = [];
                foreach ($object->getCmd() as $value_90) {
                    $values_89[] = $value_90;
                }
                $value_88 = $values_89;
            }
            if (is_string($object->getCmd())) {
                $value_88 = $object->getCmd();
            }
            $data->{'Cmd'} = $value_88;
        }
        if (null !== $object->getEntrypoint()) {
            $value_91 = $object->getEntrypoint();
            if (is_array($object->getEntrypoint())) {
                $values_92 = [];
                foreach ($object->getEntrypoint() as $value_93) {
                    $values_92[] = $value_93;
                }
                $value_91 = $values_92;
            }
            if (is_string($object->getEntrypoint())) {
                $value_91 = $object->getEntrypoint();
            }
            $data->{'Entrypoint'} = $value_91;
        }
        if (null !== $object->getMounts()) {
            $values_94 = [];
            foreach ($object->getMounts() as $value_95) {
                $values_94[] = $this->serializer->serialize($value_95, 'raw', $context);
            }
            $data->{'Mounts'} = $values_94;
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
            $values_96 = new \stdClass();
            foreach ($object->getExposedPorts() as $key_98 => $value_97) {
                $values_96->{$key_98} = $value_97;
            }
            $data->{'ExposedPorts'} = $values_96;
        }
        if (null !== $object->getHostConfig()) {
            $data->{'HostConfig'} = $this->serializer->serialize($object->getHostConfig(), 'raw', $context);
        }

        return $data;
    }
}
