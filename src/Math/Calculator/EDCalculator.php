<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\EDAdder;
use Famoser\Elliptic\Math\Calculator\Coordinator\ProjectiveCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\MultiplicationCalculator;
use Famoser\Elliptic\Math\Calculator\Swapper\ProjectiveSwapper;
use Famoser\Elliptic\Math\Primitives\ProjectiveCoordinates;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;

/**
 * Calculator for edwards curves
 *
 * @extends AbstractCalculator<ProjectiveCoordinates>
 */
class EDCalculator extends AbstractCalculator
{
    use ProjectiveCoordinator;
    use EDAdder;
    use ProjectiveSwapper;

    /** @use MultiplicationCalculator<ProjectiveCoordinates> */
    use MultiplicationCalculator;

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
