<?php

namespace Famoser\Elliptic\Math\Traits;

use Famoser\Elliptic\Primitives\Point;

trait BirationalMathTrait
{
    use InfinityPointMathTrait;

    public function add(Point $a, Point $b): Point
    {
        $mappedA = $this->birationalMap->map($this->math, $a);
        $mappedB = $this->birationalMap->map($this->math, $b);

        $mappedResult = $this->math->add($mappedA, $mappedB);

        return $this->birationalMap->reverse($this, $mappedResult);
    }

    public function double(Point $a): Point
    {
        $mappedA = $this->birationalMap->map($this->math, $a);

        $mappedResult = $this->math->double($mappedA);

        return $this->birationalMap->reverse($this, $mappedResult);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $mapped = $this->birationalMap->map($this->math, $point);

        $mappedResult = $this->math->mul($mapped, $factor);

        return $this->birationalMap->reverse($this, $mappedResult);
    }

    public function mulH(Point $point): Point
    {
        $mapped = $this->birationalMap->map($this->math, $point);

        $mappedResult = $this->math->mulH($mapped);

        return $this->birationalMap->reverse($this, $mappedResult);
    }
}
