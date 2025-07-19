<?php

namespace Famoser\Elliptic\Math\Math;

use Famoser\Elliptic\Primitives\Point;

trait BirationalMathTrait
{
    public function add(Point $a, Point $b): Point
    {
        $mappedA = $this->birationalMap->map($a);
        $mappedB = $this->birationalMap->map($b);

        $mappedResult = $this->math->add($mappedA, $mappedB);

        return $this->birationalMap->reverse($mappedResult);
    }

    public function double(Point $a): Point
    {
        $mappedA = $this->birationalMap->map($a);

        $mappedResult = $this->math->double($mappedA);

        return $this->birationalMap->reverse($mappedResult);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $mapped = $this->birationalMap->map($point);

        $mappedResult = $this->math->mul($mapped, $factor);

        return $this->birationalMap->reverse($mappedResult);
    }
}
