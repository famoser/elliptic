<?php

namespace Famoser\Elliptic\Math\Calculator\Coordinator;

use Famoser\Elliptic\Math\Calculator\Coordinator\Traits\XYZCoordinatorTrait;
use Famoser\Elliptic\Math\Primitives\ProjectiveCoordinates;
use Famoser\Elliptic\Primitives\Point;

/**
 * Projective coordinates (X,Y,Z) chosen such that affine coordinates (x=X/Z, y=Y/Z).
 */
trait ProjectiveCoordinator
{
    use XYZCoordinatorTrait;

    public function affineToNative(Point $point): ProjectiveCoordinates
    {
        // for Z = 1, it holds that X = x and Y = y
        return new ProjectiveCoordinates($point->x, $point->y, gmp_init(1));
    }

    public function nativeToAffine(ProjectiveCoordinates $nativePoint): Point
    {
        return $this->recoverAffinePoint($nativePoint->X, $nativePoint->Y, $nativePoint->Z);
    }

    public function getInfinity(): ProjectiveCoordinates
    {
        return new ProjectiveCoordinates(gmp_init(0), gmp_init(1), gmp_init(1));
    }

    public function isInfinity(ProjectiveCoordinates $point): bool
    {
        return gmp_cmp($point->Y, $point->Z) === 0 && gmp_cmp($point->Y, 0) !== 0;
    }
}
