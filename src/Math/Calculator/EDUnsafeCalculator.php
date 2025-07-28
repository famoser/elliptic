<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\EDUnsafeAdder;
use Famoser\Elliptic\Math\Calculator\Coordinator\Point01Coordinator;
use Famoser\Elliptic\Math\Calculator\Coordinator\PointCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\DoubleAndAddAlwaysMultiplicator;
use Famoser\Elliptic\Math\Calculator\Swapper\PointSwapper;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * Calculator for edwards curves
 */
class EDUnsafeCalculator extends AbstractCalculator
{
    use Point01Coordinator;
    use EDUnsafeAdder;
    use PointSwapper;
    /** @use DoubleAndAddAlwaysMultiplicator<Point> */
    use DoubleAndAddAlwaysMultiplicator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this calculator
        $check = $curve->getType() === CurveType::Edwards;
        if (!$check) {
            throw new \AssertionError('Cannot use this calculator with the chosen curve.');
        }
    }
}
