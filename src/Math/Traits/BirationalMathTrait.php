<?php

namespace Famoser\Elliptic\Math\Traits;

use Famoser\Elliptic\Primitives\Point;

trait BirationalMathTrait
{
    use InfinityPointMathTrait;

    public function add(Point $a, Point $b): Point
    {
        $mappedA = $this->birationalMap->map($this, $a);
        $mappedB = $this->birationalMap->map($this, $b);

        $mappedResult = $this->math->add($mappedA, $mappedB);

        return $this->birationalMap->reverse($this, $mappedResult);
    }

    public function double(Point $a): Point
    {
        $mappedA = $this->birationalMap->map($this, $a);

        $mappedResult = $this->math->double($mappedA);

        return $this->birationalMap->reverse($this, $mappedResult);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $mapped = $this->birationalMap->map($this, $point);

        $mappedResult = $this->math->mul($mapped, $factor);

        return $this->birationalMap->reverse($this, $mappedResult);
    }
}
