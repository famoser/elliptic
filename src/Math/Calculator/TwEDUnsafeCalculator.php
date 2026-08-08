<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\TwEDUnsafeAdder;
use Famoser\Elliptic\Math\Calculator\Coordinator\Point01Coordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\MultiplicationCalculator;
use Famoser\Elliptic\Math\Calculator\Swapper\PointSwapper;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * Calculator for twisted edwards curves, with an unsafe (in particular non-constant-time) adder
 *
 * @extends AbstractCalculator<Point>
 */
class TwEDUnsafeCalculator extends AbstractCalculator
{
    use Point01Coordinator;
    use TwEDUnsafeAdder;
    use PointSwapper;

    /** @use MultiplicationCalculator<Point> */
    use MultiplicationCalculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this calculator
        $check = $curve->getType() === CurveType::TwistedEdwards;
        if (!$check) {
            throw new \AssertionError('Cannot use this calculator with the chosen curve.');
        }
    }
}
