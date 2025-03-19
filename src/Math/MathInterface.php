<?php

namespace Mdanter\Ecc\Math;

use Mdanter\Ecc\Primitives\Curve;
use Mdanter\Ecc\Primitives\Point;

interface MathInterface
{
    public function double(Point $a): Point;
    public function add(Point $a, Point $b): Point;
    public function mul(Point $point, \GMP $factor): Point;
    public function getCurve(): Curve;
}
