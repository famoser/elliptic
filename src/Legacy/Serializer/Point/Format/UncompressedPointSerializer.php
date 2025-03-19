<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Legacy\Serializer\Point\Format;

use Mdanter\Ecc\Legacy\Primitives\CurveFpInterface;
use Mdanter\Ecc\Legacy\Primitives\PointInterface;
use Mdanter\Ecc\Legacy\Serializer\Point\PointDecodingException;
use Mdanter\Ecc\Legacy\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Legacy\Serializer\Point\PointSize;

class UncompressedPointSerializer implements PointSerializerInterface
{
    /**
     * @param PointInterface $point
     * @return string
     */
    public function serialize(PointInterface $point): string
    {
        $length = PointSize::getByteSize($point->getCurve()) * 2;

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
            throw new PointDecodingException('Invalid data: only uncompressed keys are supported.');
        }

        $point = substr($point, 2);
        $coordinateLength = strlen($point) / 2;

        $x = gmp_init(substr($point, 0, $coordinateLength), 16);
        $y = gmp_init(substr($point, $coordinateLength), 16);

        return $curve->getPoint($x, $y);
    }

    public function supportsDeserialize(string $point): bool
    {
        $prefix = substr($point, 0, 2);

        return $prefix === '04';
    }
}
