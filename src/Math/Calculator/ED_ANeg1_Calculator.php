<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\ED_ANeg1_Extended_Adder;
use Famoser\Elliptic\Math\Calculator\Coordinator\JacobiCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\DoubleAndAddAlwaysMultiplicator;
use Famoser\Elliptic\Math\Calculator\Swapper\JacobiSwapper;
use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;

/**
 * Calculator for twisted edwards curves with A = -1 mod p
 */
class ED_ANeg1_Calculator extends AbstractCalculator
{
    use JacobiCoordinator;
    use ED_ANeg1_Extended_Adder;
    use JacobiSwapper;
    /** @use DoubleAndAddAlwaysMultiplicator<JacobiPoint> */
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
