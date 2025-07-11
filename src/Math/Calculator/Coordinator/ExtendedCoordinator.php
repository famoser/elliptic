<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Coordinator;

use Famoser\Elliptic\Math\Primitives\ExtendedCoordinates;
use Famoser\Elliptic\Primitives\Point;

/**
 * Extended coordinates (X,Y,Z,T) chosen such that affine coordinates (x=X/Z,y=Y/Z,x*y=T/Z).
 */
trait ExtendedCoordinator
{
    public function affineToNative(Point $point): ExtendedCoordinates
    {
        // for Z = 1, it holds that X = x, Y = y, T = x*y
        return new ExtendedCoordinates($point->x, $point->y, gmp_init(1), gmp_mul($point->x, $point->y));
    }

    public function nativeToAffine(ExtendedCoordinates $nativePoint): Point
    {
        // to get x, need to calculate X/Z; same for y
        $zInverse = $this->field->invert($nativePoint->Z);

        // crafted inputs might be able to reach non-invertible Zs
        // we return the point at infinity for these cases
        $zInverse = $zInverse === false ? gmp_init(0) : $zInverse;

        $x = $this->field->mul($nativePoint->X, $zInverse);
        $y = $this->field->mul($nativePoint->Y, $zInverse);

        return new Point($x, $y);
    }

    public function getInfinity(): ExtendedCoordinates
    {
        return ExtendedCoordinates::createInfinity();
    }
}
