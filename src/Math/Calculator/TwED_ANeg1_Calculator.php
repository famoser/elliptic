<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\TwED_ANeg1_Extended_Adder;
use Famoser\Elliptic\Math\Calculator\Coordinator\ExtendedCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\DoubleAndAddAlwaysMultiplicator;
use Famoser\Elliptic\Math\Calculator\Swapper\ExtendedSwapper;
use Famoser\Elliptic\Math\Primitives\ExtendedCoordinates;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;

/**
 * Calculator for twisted edwards curves with A = -1 mod p
 */
class TwED_ANeg1_Calculator extends AbstractCalculator
{
    use ExtendedCoordinator;
    use TwED_ANeg1_Extended_Adder;
    use ExtendedSwapper;
    /** @use DoubleAndAddAlwaysMultiplicator<ExtendedCoordinates> */
    use DoubleAndAddAlwaysMultiplicator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this calculator
        $check = $curve->getType() === CurveType::TwistedEdwards;
        $check &= gmp_cmp($curve->getA(), gmp_sub($curve->getP(), 1)) === 0;
        if (!$check) {
            throw new \AssertionError('Cannot use this calculator with the chosen curve.');
        }
    }
}
