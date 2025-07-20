<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\ED_ANeg1_Extended_Adder;
use Famoser\Elliptic\Math\Calculator\Coordinator\ProjectiveCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\DoubleAndAddAlwaysMultiplicator;
use Famoser\Elliptic\Math\Calculator\Swapper\ProjectiveSwapper;
use Famoser\Elliptic\Math\Primitives\ProjectiveCoordinates;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;

/**
 * Calculator for edwards curves with A = -1 mod p
 */
class ED_ANeg1_Calculator extends AbstractCalculator
{
    use ProjectiveCoordinator;
    use ED_ANeg1_Extended_Adder;
    use ProjectiveSwapper;
    /** @use DoubleAndAddAlwaysMultiplicator<ProjectiveCoordinates> */
    use DoubleAndAddAlwaysMultiplicator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this calculator
        $check = $curve->getType() === CurveType::Edwards;
        $check &= gmp_cmp($curve->getA(), gmp_sub($curve->getP(), 1)) === 0;
        if (!$check) {
            throw new \AssertionError('Cannot use this calculator with the chosen curve.');
        }
    }
}
