<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Primitives\BirationalMap;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Assumes a montgomery curve with a birational map to a twisted edwards curve a=-1.
 *
 * Some hardening against side-channels has been done.
 */
class MG_TE_Math extends AbstractMath implements MathInterface
{
    private readonly TE_ANeg1_Math $math;

    public function __construct(Curve $curve, private readonly BirationalMap $birationalMap, Curve $targetCurve)
    {
        parent::__construct($curve);

        $this->math = new TE_ANeg1_Math($targetCurve);
    }

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
