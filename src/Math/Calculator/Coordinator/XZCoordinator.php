<?php

namespace Famoser\Elliptic\Math\Calculator\Coordinator;

use Famoser\Elliptic\Math\Primitives\XZPoint;
use Famoser\Elliptic\Primitives\XPoint;

/**
 * XZ coordinates (X,Z) chosen such that affine coordinates (x=X/Z).
 */
trait XZCoordinator
{
    public function affineToNative(XPoint $point): XZPoint
    {
        // for Z = 1, it holds that X = x
        return new XZPoint($point->x, gmp_init(1));
    }

    public function nativeToAffine(XZPoint $nativePoint): XPoint
    {
        // to get x, need to calculate X/Z
        $zInverse = $this->field->invert($nativePoint->Z) ?: gmp_init(0);
        $x = $this->field->mul($nativePoint->X, $zInverse);

        return new XPoint($x);
    }

    public function getInfinity(): XPoint
    {
        return XPoint::createInfinity();
    }
}
