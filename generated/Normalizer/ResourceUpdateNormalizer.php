<?php

namespace Docker\API\Normalizer;

use Joli\Jane\Reference\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer;

class ResourceUpdateNormalizer extends SerializerAwareNormalizer implements DenormalizerInterface, NormalizerInterface
{
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== 'Docker\\API\\Model\\ResourceUpdate') {
            return false;
        }

        return true;
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($data instanceof \Docker\API\Model\ResourceUpdate) {
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
        $object = new \Docker\API\Model\ResourceUpdate();
        if (!isset($context['rootSchema'])) {
            $context['rootSchema'] = $object;
        }
        if (isset($data->{'BlkioWeight'})) {
            $object->setBlkioWeight($data->{'BlkioWeight'});
        }
        if (isset($data->{'CpuShares'})) {
            $object->setCpuShares($data->{'CpuShares'});
        }
        if (isset($data->{'CpuPeriod'})) {
            $object->setCpuPeriod($data->{'CpuPeriod'});
        }
        if (isset($data->{'CpuQuota'})) {
            $object->setCpuQuota($data->{'CpuQuota'});
        }
        if (isset($data->{'CpusetCpus'})) {
            $object->setCpusetCpus($data->{'CpusetCpus'});
        }
        if (isset($data->{'CpusetMems'})) {
            $object->setCpusetMems($data->{'CpusetMems'});
        }
        if (isset($data->{'Memory'})) {
            $object->setMemory($data->{'Memory'});
        }
        if (isset($data->{'MemorySwap'})) {
            $object->setMemorySwap($data->{'MemorySwap'});
        }
        if (isset($data->{'MemoryReservation'})) {
            $object->setMemoryReservation($data->{'MemoryReservation'});
        }
        if (isset($data->{'KernelMemory'})) {
            $object->setKernelMemory($data->{'KernelMemory'});
        }

        return $object;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = new \stdClass();
        if (null !== $object->getBlkioWeight()) {
            $data->{'BlkioWeight'} = $object->getBlkioWeight();
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

        return $data;
    }
}
