<?php

namespace Mdanter\Ecc\Legacy\Serializer\Point;

use Mdanter\Ecc\Legacy\Curves\NamedCurveFp;
use Mdanter\Ecc\Legacy\Curves\NistCurve;
use Mdanter\Ecc\Legacy\Curves\SecpCurves;
use Mdanter\Ecc\Legacy\Exception\UnsupportedCurveException;
use Mdanter\Ecc\Legacy\Primitives\CurveFpInterface;

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
        SecpCurves::NAME_SECP_192R1 => 24,
        SecpCurves::NAME_SECP_192K1 => 24,
        SecpCurves::NAME_SECP_224R1 => 28,
        SecpCurves::NAME_SECP_224K1 => 28,
        SecpCurves::NAME_SECP_256R1 => 32,
        SecpCurves::NAME_SECP_256K1 => 32,
        SecpCurves::NAME_SECP_384R1 => 48,
        SecpCurves::NAME_SECP_521R1 => 66,
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
