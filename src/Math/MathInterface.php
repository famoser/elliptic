<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

interface MathInterface
{
    public function double(Point $a): Point;
    public function add(Point $a, Point $b): Point;
    public function mul(Point $point, \GMP $factor): Point;
    public function getCurve(): Curve;
}
