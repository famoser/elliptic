<?php

namespace Famoser\Elliptic\Math\Calculator\Coordinator;

use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Primitives\Point;

/**
 * Jacobi coordinates (X,Y,Z) chosen such that affine coordinates (x=X/Z, y=Y/Z).
 */
trait JacobiCoordinator
{
    public function affineToNative(Point $point): JacobiPoint
    {
        // for Z = 1, it holds that X = x and Y = y
        return new JacobiPoint($point->x, $point->y, gmp_init(1));
    }

    public function nativeToAffine(JacobiPoint $nativePoint): Point
    {
        // to get x, need to calculate X/Z; same for y
        $zInverse = $this->field->invert($nativePoint->Z) ?: gmp_init(0);
        $x = $this->field->mul($nativePoint->X, $zInverse);
        $y = $this->field->mul($nativePoint->Y, $zInverse);

        return new Point($x, $y);
    }

    public function getInfinity(): JacobiPoint
    {
        return JacobiPoint::createInfinity();
    }
}
