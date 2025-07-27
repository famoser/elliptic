<?php

namespace Famoser\Elliptic\Math\Traits;

use Famoser\Elliptic\Primitives\Point;

trait InfinityPointMathTrait
{
    public function isInfinity(Point $a): bool
    {
        return $this->math->isInfinity($a);
    }

    public function getInfinity(): Point
    {
        return $this->math->getInfinity();
    }
}
