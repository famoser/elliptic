<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Serializer\Point\Format;

use Mdanter\Ecc\Primitives\CurveFpInterface;
use Mdanter\Ecc\Primitives\PointInterface;
use Mdanter\Ecc\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Serializer\Util\CurveOidMapper;
use Mdanter\Ecc\Util\BinaryString;

class UncompressedPointSerializer implements PointSerializerInterface
{
    /**
     * @param PointInterface $point
     * @return string
     */
    public function serialize(PointInterface $point): string
    {
        $length = CurveOidMapper::getByteSize($point->getCurve()) * 2;

        $hexString = '04';
        $hexString .= str_pad(gmp_strval($point->getX(), 16), $length, '0', STR_PAD_LEFT);
        $hexString .= str_pad(gmp_strval($point->getY(), 16), $length, '0', STR_PAD_LEFT);

        return $hexString;
    }

    /**
     * @param CurveFpInterface $curve
     * @param string           $point
     * @return PointInterface
     */
    public function deserialize(CurveFpInterface $curve, string $point): PointInterface
    {
        if (!$this->supportsDeserialize($point)) {
            throw new \InvalidArgumentException('Invalid data: only uncompressed keys are supported.');
        }

        $point = BinaryString::substring($point, 2);
        $dataLength = BinaryString::length($point);

        $x = gmp_init(BinaryString::substring($point, 0, $dataLength / 2), 16);
        $y = gmp_init(BinaryString::substring($point, $dataLength / 2), 16);

        return $curve->getPoint($x, $y);
    }

    public function supportsDeserialize(string $point): bool
    {
        $prefix = substr($point, 0, 2);

        return $prefix === '04';
    }
}
