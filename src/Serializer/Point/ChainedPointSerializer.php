<?php

namespace Mdanter\Ecc\Serializer\Point;

use Mdanter\Ecc\Math\MathAdapterFactory;
use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\PointInterface;
use Mdanter\Ecc\Serializer\Point\Format\CompressedPointSerializer;
use Mdanter\Ecc\Serializer\Point\Format\UncompressedPointSerializer;

class ChainedPointSerializer implements PointSerializerInterface
{
    /**
     * @param PointSerializerInterface[] $serializers
     */
    public function __construct(private readonly array $serializers)
    {
        if (count($this->serializers) === 0) {
            throw new \LogicException('At least one serializer must be available.');
        }
    }

    public static function create(): self
    {
        $adapter = MathAdapterFactory::getAdapter();
        return new self([new CompressedPointSerializer($adapter), new UncompressedPointSerializer()]);
    }

    public function serialize(PointInterface $point): string
    {
        return $this->serializers[0]->serialize($point);
    }

    public function deserialize(CurveFpInterface $curve, string $point): PointInterface
    {
        foreach ($this->serializers as $serializer) {
            if ($serializer->supportsDeserialize($point)) {
                return $serializer->deserialize($curve, $point);
            }
        }

        throw new \InvalidArgumentException('No serializer available for this point.');
    }

    public function supportsDeserialize(string $point): bool
    {
        foreach ($this->serializers as $serializer) {
            if ($serializer->supportsDeserialize($point)) {
                return true;
            }
        }

        throw new \InvalidArgumentException('No serializer available for this point.');
    }
}
