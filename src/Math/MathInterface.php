<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

interface MathInterface
{
    public function getCurve(): Curve;

    public function isInfinity(Point $point): bool;
    public function getInfinity(): Point;

    public function double(Point $a): Point;
    public function add(Point $a, Point $b): Point;
    public function mulG(\GMP $factor): Point;
    public function mul(Point $point, \GMP $factor): Point;
}
