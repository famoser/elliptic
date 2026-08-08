<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

interface MathInterface
{
    public function getCurve(): Curve;

    public function isInfinity(Point $point): bool;
    public function getInfinity(): Point;

    public function add(Point $a, Point $b): Point;
    public function double(Point $a): Point;
    public function mul(Point $point, \GMP $factor): Point;

    /**
     * multiplies over the basepoint G, which potentially unlocks optimizations
     *
     * note that so far, no optimization is implemented
     */
    public function mulG(\GMP $factor): Point;

    /**
     * multiplies by the cofactor H
     *
     * note that if the cofactor is 1, returns the same point
     */
    public function mulH(Point $point): Point;
}
