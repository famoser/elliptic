<?php

namespace Famoser\Elliptic\Math\Calculator\Coordinator;

use Famoser\Elliptic\Primitives\Point;

trait Point01Coordinator
{
    public function getInfinity(): Point
    {
        return new Point(gmp_init(0), gmp_init(1));
    }

    public function isInfinity(Point $point): bool
    {
        return gmp_cmp($point->x, 0) === 0 && gmp_cmp($point->y, 1) === 0;
    }
}
