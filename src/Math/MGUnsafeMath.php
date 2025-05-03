<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\MGUnsafeCalculator;
use Famoser\Elliptic\Math\Calculator\SWUnsafeCalculator;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Supports Montgomery curves by implementing their default calculation rules.
 *
 * This is in general unsafe, as not hardened against side-channels.
 */
class MGUnsafeMath extends AbstractMath implements MathInterface
{
    private readonly MGUnsafeCalculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new MGUnsafeCalculator($curve);
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
