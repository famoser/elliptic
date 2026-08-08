<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\MGUnsafeAdder;
use Famoser\Elliptic\Math\Calculator\Coordinator\PointCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\MultiplicationCalculator;
use Famoser\Elliptic\Math\Calculator\Swapper\PointSwapper;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;
use Famoser\Elliptic\Primitives\Point;

/**
 * Calculator for montgomery curves, with an unsafe (in particular non-constant-time) adder
 *
 * @extends AbstractCalculator<Point>
 */
class MGUnsafeCalculator extends AbstractCalculator
{
    use PointCoordinator;
    use MGUnsafeAdder;
    use PointSwapper;

    /** @use MultiplicationCalculator<Point> */
    use MultiplicationCalculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this calculator
        $check = $curve->getType() === CurveType::Montgomery;
        if (!$check) {
            throw new \AssertionError('Cannot use this calculator with the chosen curve.');
        }
    }
}
