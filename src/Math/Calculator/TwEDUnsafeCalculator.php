<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\TwEDUnsafeAdder;
use Famoser\Elliptic\Math\Calculator\Coordinator\Point01Coordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\DoubleAndAddAlwaysMultiplicator;
use Famoser\Elliptic\Math\Calculator\Swapper\PointSwapper;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * Calculator for twisted edwards curves
 */
class TwEDUnsafeCalculator extends AbstractCalculator
{
    use Point01Coordinator;
    use TwEDUnsafeAdder;
    use PointSwapper;
    /** @use DoubleAndAddAlwaysMultiplicator<Point> */
    use DoubleAndAddAlwaysMultiplicator;

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
