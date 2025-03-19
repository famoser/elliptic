<?php

namespace Mdanter\Ecc\Serializer;

use Mdanter\Ecc\Primitives\Curve;
use Mdanter\Ecc\Primitives\Point;

class PointDecoder
{
    public function __construct(private readonly Curve $curve)
    {
    }

    /**
     * @throws PointDecoderException
     */
    public function fromCoordinates(\GMP $x, \GMP $y): Point
    {
        $point = new Point($x, $y);
        if (!$this->fulfillsDefiningEquationOfCurve($point)) {
            throw new PointDecoderException('Point not on curve.');
        }

        return $point;
    }

    /**
     * @throws PointDecoderException
     */
    public function fromXCoordinate(\GMP $x, bool $evenY)
    {
        throw new PointDecoderException('Not yet implemented.');
    }

    /**
     * check fulfills defining equation of the curve
     */
    private function fulfillsDefiningEquationOfCurve(Point $point): bool
    {
        $left = gmp_pow($point->y, 2);
        $right = gmp_add(
            gmp_add(
                gmp_pow($point->x, 3),
                gmp_mul($this->curve->getA(), $point->x)
            ),
            $this->curve->getB()
        );

        $comparison = gmp_mod(
            gmp_sub($left, $right),
            $this->curve->getP()
        );

        return gmp_cmp($comparison, 0) == 0;
    }
}
