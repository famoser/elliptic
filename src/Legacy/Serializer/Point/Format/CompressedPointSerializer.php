<?php
declare(strict_types=1);

namespace Mdanter\Ecc\Legacy\Serializer\Point\Format;

use Mdanter\Ecc\Legacy\Math\GmpMathInterface;
use Mdanter\Ecc\Legacy\Primitives\CurveFpInterface;
use Mdanter\Ecc\Legacy\Primitives\PointInterface;
use Mdanter\Ecc\Legacy\Serializer\Point\PointDecodingException;
use Mdanter\Ecc\Legacy\Serializer\Point\PointSerializerInterface;
use Mdanter\Ecc\Legacy\Serializer\Point\PointSize;

class CompressedPointSerializer implements PointSerializerInterface
{
    public function __construct(private readonly GmpMathInterface $adapter)
    {
    }

    /**
     * @param PointInterface $point
     * @return string
     */
    public function serialize(PointInterface $point): string
    {
        $isEven = $this->adapter->equals($this->adapter->mod($point->getY(), gmp_init(2, 10)), gmp_init(0));
        $length = PointSize::getByteSize($point->getCurve()) * 2;

        $hexString = $isEven ? '02' : '03';
        $hexString .= str_pad(gmp_strval($point->getX(), 16), $length, '0', STR_PAD_LEFT);

        return $hexString;
    }

    /**
     * @param CurveFpInterface $curve
     * @param string $point - hex serialized compressed point
     * @return PointInterface
     */
    public function deserialize(CurveFpInterface $curve, string $point): PointInterface
    {
        if (!$this->supportsDeserialize($point)) {
            throw new PointDecodingException('Invalid data: only compressed keys are supported.');
        }

        $x = gmp_init(substr($point, 2), 16);
        $y = $curve->recoverYfromX(str_starts_with($point, '03'), $x);

        return $curve->getPoint($x, $y);
    }

    public function supportsDeserialize(string $point): bool
    {
        $prefix = substr($point, 0, 2);

        return $prefix === '03' || $prefix === '02';
    }
}
