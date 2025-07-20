<?php

namespace Famoser\Elliptic\Serializer\PointDecoder;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromCoordinatesTrait;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\FromXCoordinateTrait;
use Famoser\Elliptic\Serializer\PointDecoder\Traits\PMod85RecoveryTrait;
use Famoser\Elliptic\Serializer\SEC\SECPointDecoderInterface;

class MGPointDecoder implements SECPointDecoderInterface
{
    use FromCoordinatesTrait;
    use FromXCoordinateTrait;

    public function __construct(private readonly Curve $curve)
    {
        // check allowed to use this decoder
        $check = $curve->getType() === CurveType::Montgomery;
        if (!$check) {
            throw new \AssertionError('Cannot use this decoder with the chosen curve.');
        }
    }

    /**
     * calculate b * y^2
     */
    private function calculateLeftSide(\GMP $y): \GMP
    {
        return gmp_mul(
            $this->curve->getB(),
            gmp_pow($y, 2)
        );
    }

    /**
     * calculate x^3 + ax^2 + x
     */
    private function calculateRightSide(\GMP $x): \GMP
    {
        return gmp_add(
            gmp_add(
                gmp_powm($x, 3, $this->curve->getP()),
                gmp_mul(
                    $this->curve->getA(),
                    gmp_pow($x, 2)
                )
            ),
            $x
        );
    }
}
