<?php

namespace Famoser\Elliptic\Math\Calculator\Coordinator;

use Famoser\Elliptic\Math\Calculator\Coordinator\Traits\XYZCoordinatorTrait;
use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Primitives\Point;

/**
 * Jacobi coordinates (X,Y,Z) chosen such that affine coordinates (x=X/Z, y=Y/Z).
 */
trait JacobiCoordinator
{
    use XYZCoordinatorTrait;

    public function affineToNative(Point $point): JacobiPoint
    {
        // for Z = 1, it holds that X = x and Y = y
        return new JacobiPoint($point->x, $point->y, gmp_init(1));
    }

    public function nativeToAffine(JacobiPoint $nativePoint): Point
    {
        return $this->recoverAffinePoint($nativePoint->X, $nativePoint->Y, $nativePoint->Z);
    }

    public function getInfinity(): JacobiPoint
    {
        return new JacobiPoint(gmp_init(0), gmp_init(1), gmp_init(0));
    }

    public function isInfinity(JacobiPoint $point): bool
    {
        return gmp_cmp($point->Z, 0) === 0;
    }
}
