<?php

namespace Famoser\Elliptic\Math\Traits;

use Famoser\Elliptic\Primitives\Point;

trait MathTrait
{
    public function isInfinity(Point $point): bool
    {
        return $this->calculator->isInfinity($point);
    }

    public function getInfinity(): Point
    {
        return $this->calculator->getInfinity();
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        return $this->calculator->mul($point, $factor);
    }

    public function double(Point $a): Point
    {
        return $this->calculator->double($a);
    }

    public function add(Point $a, Point $b): Point
    {
        return $this->calculator->add($a, $b);
    }
}
