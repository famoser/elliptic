<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Coordinator;

use Famoser\Elliptic\Math\Calculator\Coordinator\Traits\XYZCoordinatorTrait;
use Famoser\Elliptic\Math\Primitives\ExtendedCoordinates;
use Famoser\Elliptic\Primitives\Point;

/**
 * Extended coordinates (X,Y,Z,T) chosen such that affine coordinates (x=X/Z,y=Y/Z,x*y=T/Z).
 */
trait ExtendedCoordinator
{
    use XYZCoordinatorTrait;

    public function affineToNative(Point $point): ExtendedCoordinates
    {
        // for Z = 1, it holds that X = x, Y = y, T = x*y
        if (gmp_cmp($point->x, 0) === 0) {
            return $this->getInfinity();
        }

        return new ExtendedCoordinates($point->x, $point->y, gmp_init(1), gmp_mul($point->x, $point->y));
    }

    public function nativeToAffine(ExtendedCoordinates $nativePoint): Point
    {
        return $this->recoverAffinePoint($nativePoint->X, $nativePoint->Y, $nativePoint->Z);
    }

    public function getInfinity(): ExtendedCoordinates
    {
        return new ExtendedCoordinates(gmp_init(0), gmp_init(1), gmp_init(1), gmp_init(0));
    }

    public function isInfinity(ExtendedCoordinates $point): bool
    {
        return gmp_cmp($point->Y, $point->Z) === 0 && gmp_cmp($point->Y, 0) !== 0;
    }
}
