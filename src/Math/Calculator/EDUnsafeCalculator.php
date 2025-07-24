<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\EDAdder;
use Famoser\Elliptic\Math\Calculator\Adder\EDUnsafeAdder;
use Famoser\Elliptic\Math\Calculator\Coordinator\PointCoordinator;
use Famoser\Elliptic\Math\Calculator\Coordinator\ProjectiveCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\DoubleAndAddAlwaysMultiplicator;
use Famoser\Elliptic\Math\Calculator\Swapper\PointSwapper;
use Famoser\Elliptic\Math\Calculator\Swapper\ProjectiveSwapper;
use Famoser\Elliptic\Math\Primitives\ProjectiveCoordinates;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;

/**
 * Calculator for edwards curves
 */
class EDUnsafeCalculator extends AbstractCalculator
{
    use PointCoordinator;
    use EDUnsafeAdder;
    use PointSwapper;
    /** @use DoubleAndAddAlwaysMultiplicator<ProjectiveCoordinates> */
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
