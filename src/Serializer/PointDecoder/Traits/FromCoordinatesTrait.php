<?php

namespace Famoser\Elliptic\Serializer\PointDecoder\Traits;

use Famoser\Elliptic\Primitives\Point;
use Famoser\Elliptic\Serializer\PointDecoder\PointDecoderException;

trait FromCoordinatesTrait
{
    /**
     * @throws PointDecoderException
     */
    public function fromCoordinates(\GMP $x, \GMP $y): Point
    {
        $point = new Point($x, $y);

        if (!$this->fulfillsDefiningEquation($point)) {
            throw new PointDecoderException('Point not on curve.');
        }

        return $point;
    }

    private function fulfillsDefiningEquation(Point $point): bool
    {
        $left = $this->calculateLeftSide($point);
        $right = $this->calculateRightSide($point);

        $comparison = gmp_mod(
            gmp_sub($left, $right),
            $this->curve->getP()
        );

        return gmp_cmp($comparison, 0) == 0;
    }
}
