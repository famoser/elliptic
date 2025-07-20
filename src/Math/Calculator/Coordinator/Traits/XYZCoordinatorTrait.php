<?php

/** @noinspection DuplicatedCode */

namespace Famoser\Elliptic\Math\Calculator\Coordinator\Traits;

use Famoser\Elliptic\Primitives\Point;

/**
 * Recover affine point (x,y) given native coordinates (x=X/Z,y=Y/Z).
 */
trait XYZCoordinatorTrait
{
    public function recoverAffinePoint(\GMP $X, \GMP $Y, \GMP $Z): Point
    {
        // to get x, need to calculate X/Z; same for y
        $zInverse = $this->field->invert($Z);

        // crafted inputs might be able to reach non-invertible Zs
        // we return the point at infinity for these cases
        $zInverse = $zInverse === false ? gmp_init(0) : $zInverse;

        $x = $this->field->mul($X, $zInverse);
        $y = $this->field->mul($Y, $zInverse);

        return new Point($x, $y);
    }
}
