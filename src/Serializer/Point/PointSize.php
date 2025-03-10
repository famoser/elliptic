<?php

namespace Mdanter\Ecc\Serializer\Point;

use Mdanter\Ecc\Curves\NamedCurveFp;
use Mdanter\Ecc\Curves\NistCurve;
use Mdanter\Ecc\Curves\SecgCurve;
use Mdanter\Ecc\Exception\UnsupportedCurveException;
use Mdanter\Ecc\Primitives\CurveFpInterface;

class PointSize
{
    /**
     * @var array
     */
    private static array $sizeMap = array(
        NistCurve::NAME_P192 => 24,
        NistCurve::NAME_P224 => 28,
        NistCurve::NAME_P256 => 32,
        NistCurve::NAME_P384 => 48,
        NistCurve::NAME_P521 => 66,
        SecgCurve::NAME_SECP_112R1 => 14,
        SecgCurve::NAME_SECP_192K1 => 24,
        SecgCurve::NAME_SECP_256K1 => 32,
        SecgCurve::NAME_SECP_256R1 => 32,
        SecgCurve::NAME_SECP_384R1 => 48,
    );

    /**
     * @param CurveFpInterface $curve
     * @return int
     */
    public static function getByteSize(CurveFpInterface $curve): int
    {
        if ($curve instanceof NamedCurveFp && array_key_exists($curve->getName(), self::$sizeMap)) {
            return self::$sizeMap[$curve->getName()];
        }

        throw new UnsupportedCurveException('Unsupported curve type');
    }
}
