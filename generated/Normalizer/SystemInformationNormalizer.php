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
        if (isset($data->{'Containers'})) {
            $object->setContainers($data->{'Containers'});
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
            $values_157 = [];
            foreach ($data->{'DriverStatus'} as $value_158) {
                $values_159 = [];
                foreach ($value_158 as $value_160) {
                    $values_159[] = $value_160;
                }
                $values_157[] = $values_159;
            }
            $object->setDriverStatus($values_157);
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
            $values_161 = [];
            foreach ($data->{'Labels'} as $value_162) {
                $values_161[] = $value_162;
            }
            $object->setLabels($values_161);
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
        if (null !== $object->getContainers()) {
            $data->{'Containers'} = $object->getContainers();
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
            $values_163 = [];
            foreach ($object->getDriverStatus() as $value_164) {
                $values_165 = [];
                foreach ($value_164 as $value_166) {
                    $values_165[] = $value_166;
                }
                $values_163[] = $values_165;
            }
            $data->{'DriverStatus'} = $values_163;
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
            $values_167 = [];
            foreach ($object->getLabels() as $value_168) {
                $values_167[] = $value_168;
            }
            $data->{'Labels'} = $values_167;
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
