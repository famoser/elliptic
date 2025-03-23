<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Algorithm\DoubleAndAddAlways;
use Famoser\Elliptic\Math\Calculator\UnsafePrimeCurveCalculator;
use Famoser\Elliptic\Math\Utils\ConstSwapper;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

class UnsafePrimeCurveMath extends BaseMath implements MathInterface
{
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
