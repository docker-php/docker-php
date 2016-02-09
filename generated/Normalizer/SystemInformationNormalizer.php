<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class SystemInformationNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\SystemInformation') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\SystemInformation) {
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
        $object = new \Docker\API\Model\SystemInformation();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'Architecture'})) {
            $object->setArchitecture($data->{'Architecture'});
        }
        if (isset($data->{'Containers'})) {
            $object->setContainers($data->{'Containers'});
        }
        if (isset($data->{'ContainersRunning'})) {
            $object->setContainersRunning($data->{'ContainersRunning'});
        }
        if (isset($data->{'ContainersStopped'})) {
            $object->setContainersStopped($data->{'ContainersStopped'});
        }
        if (isset($data->{'ContainersPaused'})) {
            $object->setContainersPaused($data->{'ContainersPaused'});
        }
        if (isset($data->{'CpuCfsPeriod'})) {
            $object->setCpuCfsPeriod($data->{'CpuCfsPeriod'});
        }
        if (isset($data->{'CpuCfsQuota'})) {
            $object->setCpuCfsQuota($data->{'CpuCfsQuota'});
        }
        if (isset($data->{'Debug'})) {
            $object->setDebug($data->{'Debug'});
        }
        if (isset($data->{'DiscoveryBackend'})) {
            $object->setDiscoveryBackend($data->{'DiscoveryBackend'});
        }
        if (isset($data->{'DockerRootDir'})) {
            $object->setDockerRootDir($data->{'DockerRootDir'});
        }
        if (isset($data->{'Driver'})) {
            $object->setDriver($data->{'Driver'});
        }
        if (isset($data->{'DriverStatus'})) {
            $values_185 = [];
            foreach ($data->{'DriverStatus'} as $value_186) {
                $values_187 = [];
                foreach ($value_186 as $value_188) {
                    $values_187[] = $value_188;
                }
                $values_185[] = $values_187;
            }
            $object->setDriverStatus($values_185);
        }
        if (isset($data->{'SystemStatus'})) {
            $values_189 = [];
            foreach ($data->{'SystemStatus'} as $value_190) {
                $values_191 = [];
                foreach ($value_190 as $value_192) {
                    $values_191[] = $value_192;
                }
                $values_189[] = $values_191;
            }
            $object->setSystemStatus($values_189);
        }
        if (isset($data->{'ExecutionDriver'})) {
            $object->setExecutionDriver($data->{'ExecutionDriver'});
        }
        if (isset($data->{'ExperimentalBuild'})) {
            $object->setExperimentalBuild($data->{'ExperimentalBuild'});
        }
        if (isset($data->{'HttpProxy'})) {
            $object->setHttpProxy($data->{'HttpProxy'});
        }
        if (isset($data->{'HttpsProxy'})) {
            $object->setHttpsProxy($data->{'HttpsProxy'});
        }
        if (isset($data->{'ID'})) {
            $object->setID($data->{'ID'});
        }
        if (isset($data->{'IPv4Forwarding'})) {
            $object->setIPv4Forwarding($data->{'IPv4Forwarding'});
        }
        if (isset($data->{'Images'})) {
            $object->setImages($data->{'Images'});
        }
        if (isset($data->{'IndexServerAddress'})) {
            $object->setIndexServerAddress($data->{'IndexServerAddress'});
        }
        if (isset($data->{'InitPath'})) {
            $object->setInitPath($data->{'InitPath'});
        }
        if (isset($data->{'InitSha1'})) {
            $object->setInitSha1($data->{'InitSha1'});
        }
        if (isset($data->{'KernelVersion'})) {
            $object->setKernelVersion($data->{'KernelVersion'});
        }
        if (isset($data->{'Labels'})) {
            $values_193 = [];
            foreach ($data->{'Labels'} as $value_194) {
                $values_193[] = $value_194;
            }
            $object->setLabels($values_193);
        }
        if (isset($data->{'MemTotal'})) {
            $object->setMemTotal($data->{'MemTotal'});
        }
        if (isset($data->{'MemoryLimit'})) {
            $object->setMemoryLimit($data->{'MemoryLimit'});
        }
        if (isset($data->{'NCPU'})) {
            $object->setNCPU($data->{'NCPU'});
        }
        if (isset($data->{'NEventsListener'})) {
            $object->setNEventsListener($data->{'NEventsListener'});
        }
        if (isset($data->{'NFd'})) {
            $object->setNFd($data->{'NFd'});
        }
        if (isset($data->{'NGoroutines'})) {
            $object->setNGoroutines($data->{'NGoroutines'});
        }
        if (isset($data->{'Name'})) {
            $object->setName($data->{'Name'});
        }
        if (isset($data->{'NoProxy'})) {
            $object->setNoProxy($data->{'NoProxy'});
        }
        if (isset($data->{'OomKillDisable'})) {
            $object->setOomKillDisable($data->{'OomKillDisable'});
        }
        if (isset($data->{'OSType'})) {
            $object->setOSType($data->{'OSType'});
        }
        if (isset($data->{'OperatingSystem'})) {
            $object->setOperatingSystem($data->{'OperatingSystem'});
        }
        if (isset($data->{'RegistryConfig'})) {
            $object->setRegistryConfig($this->serializer->deserialize($data->{'RegistryConfig'}, 'Docker\\API\\Model\\RegistryConfig', 'raw', $context));
        }
        if (isset($data->{'SwapLimit'})) {
            $object->setSwapLimit($data->{'SwapLimit'});
        }
        if (isset($data->{'SystemTime'})) {
            $object->setSystemTime($data->{'SystemTime'});
        }
        if (isset($data->{'ServerVersion'})) {
            $object->setServerVersion($data->{'ServerVersion'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getArchitecture()) {
            $data->{'Architecture'} = $object->getArchitecture();
        }
        if (null !== $object->getContainers()) {
            $data->{'Containers'} = $object->getContainers();
        }
        if (null !== $object->getContainersRunning()) {
            $data->{'ContainersRunning'} = $object->getContainersRunning();
        }
        if (null !== $object->getContainersStopped()) {
            $data->{'ContainersStopped'} = $object->getContainersStopped();
        }
        if (null !== $object->getContainersPaused()) {
            $data->{'ContainersPaused'} = $object->getContainersPaused();
        }
        if (null !== $object->getCpuCfsPeriod()) {
            $data->{'CpuCfsPeriod'} = $object->getCpuCfsPeriod();
        }
        if (null !== $object->getCpuCfsQuota()) {
            $data->{'CpuCfsQuota'} = $object->getCpuCfsQuota();
        }
        if (null !== $object->getDebug()) {
            $data->{'Debug'} = $object->getDebug();
        }
        if (null !== $object->getDiscoveryBackend()) {
            $data->{'DiscoveryBackend'} = $object->getDiscoveryBackend();
        }
        if (null !== $object->getDockerRootDir()) {
            $data->{'DockerRootDir'} = $object->getDockerRootDir();
        }
        if (null !== $object->getDriver()) {
            $data->{'Driver'} = $object->getDriver();
        }
        if (null !== $object->getDriverStatus()) {
            $values_195 = [];
            foreach ($object->getDriverStatus() as $value_196) {
                $values_197 = [];
                foreach ($value_196 as $value_198) {
                    $values_197[] = $value_198;
                }
                $values_195[] = $values_197;
            }
            $data->{'DriverStatus'} = $values_195;
        }
        if (null !== $object->getSystemStatus()) {
            $values_199 = [];
            foreach ($object->getSystemStatus() as $value_200) {
                $values_201 = [];
                foreach ($value_200 as $value_202) {
                    $values_201[] = $value_202;
                }
                $values_199[] = $values_201;
            }
            $data->{'SystemStatus'} = $values_199;
        }
        if (null !== $object->getExecutionDriver()) {
            $data->{'ExecutionDriver'} = $object->getExecutionDriver();
        }
        if (null !== $object->getExperimentalBuild()) {
            $data->{'ExperimentalBuild'} = $object->getExperimentalBuild();
        }
        if (null !== $object->getHttpProxy()) {
            $data->{'HttpProxy'} = $object->getHttpProxy();
        }
        if (null !== $object->getHttpsProxy()) {
            $data->{'HttpsProxy'} = $object->getHttpsProxy();
        }
        if (null !== $object->getID()) {
            $data->{'ID'} = $object->getID();
        }
        if (null !== $object->getIPv4Forwarding()) {
            $data->{'IPv4Forwarding'} = $object->getIPv4Forwarding();
        }
        if (null !== $object->getImages()) {
            $data->{'Images'} = $object->getImages();
        }
        if (null !== $object->getIndexServerAddress()) {
            $data->{'IndexServerAddress'} = $object->getIndexServerAddress();
        }
        if (null !== $object->getInitPath()) {
            $data->{'InitPath'} = $object->getInitPath();
        }
        if (null !== $object->getInitSha1()) {
            $data->{'InitSha1'} = $object->getInitSha1();
        }
        if (null !== $object->getKernelVersion()) {
            $data->{'KernelVersion'} = $object->getKernelVersion();
        }
        if (null !== $object->getLabels()) {
            $values_203 = [];
            foreach ($object->getLabels() as $value_204) {
                $values_203[] = $value_204;
            }
            $data->{'Labels'} = $values_203;
        }
        if (null !== $object->getMemTotal()) {
            $data->{'MemTotal'} = $object->getMemTotal();
        }
        if (null !== $object->getMemoryLimit()) {
            $data->{'MemoryLimit'} = $object->getMemoryLimit();
        }
        if (null !== $object->getNCPU()) {
            $data->{'NCPU'} = $object->getNCPU();
        }
        if (null !== $object->getNEventsListener()) {
            $data->{'NEventsListener'} = $object->getNEventsListener();
        }
        if (null !== $object->getNFd()) {
            $data->{'NFd'} = $object->getNFd();
        }
        if (null !== $object->getNGoroutines()) {
            $data->{'NGoroutines'} = $object->getNGoroutines();
        }
        if (null !== $object->getName()) {
            $data->{'Name'} = $object->getName();
        }
        if (null !== $object->getNoProxy()) {
            $data->{'NoProxy'} = $object->getNoProxy();
        }
        if (null !== $object->getOomKillDisable()) {
            $data->{'OomKillDisable'} = $object->getOomKillDisable();
        }
        if (null !== $object->getOSType()) {
            $data->{'OSType'} = $object->getOSType();
        }
        if (null !== $object->getOperatingSystem()) {
            $data->{'OperatingSystem'} = $object->getOperatingSystem();
        }
        if (null !== $object->getRegistryConfig()) {
            $data->{'RegistryConfig'} = $this->serializer->serialize($object->getRegistryConfig(), 'raw', $context);
        }
        if (null !== $object->getSwapLimit()) {
            $data->{'SwapLimit'} = $object->getSwapLimit();
        }
        if (null !== $object->getSystemTime()) {
            $data->{'SystemTime'} = $object->getSystemTime();
        }
        if (null !== $object->getServerVersion()) {
            $data->{'ServerVersion'} = $object->getServerVersion();
        }

        return $data;
    }
}
