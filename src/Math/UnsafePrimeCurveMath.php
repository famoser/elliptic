<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Algorithm\DoubleAndAddAlways;
use Famoser\Elliptic\Math\Calculator\UnsafePrimeCurveCalculator;
use Famoser\Elliptic\Math\Utils\ConstSwapper;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Supports all prime curves by implementing their default calculation rules.
 * This is in general unsafe, as hardening towards side-channels is out of scope.
 *
 * @extends BaseMath<Point>
 */
class UnsafePrimeCurveMath extends BaseMath implements MathInterface
{
    /** @var DoubleAndAddAlways<Point>  */
    private readonly DoubleAndAddAlways $doubleAndAddAlways;

    public function __construct(Curve $curve)
    {
        $calculator = new UnsafePrimeCurveCalculator($curve, new ConstSwapper());
        parent::__construct($calculator);

        $this->doubleAndAddAlways = new DoubleAndAddAlways($calculator);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        return $this->doubleAndAddAlways->mul($point, $factor);
    }
}
