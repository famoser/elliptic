<?php

namespace Famoser\Elliptic\Math\Calculator;

use Famoser\Elliptic\Math\Calculator\Adder\SW_ANeg3_Jacobi_Adder;
use Famoser\Elliptic\Math\Calculator\Coordinator\JacobiCoordinator;
use Famoser\Elliptic\Math\Calculator\Multiplicator\DoubleAndAddAlwaysMultiplicator;
use Famoser\Elliptic\Math\Calculator\Swapper\JacobiSwapper;
use Famoser\Elliptic\Math\Primitives\JacobiPoint;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\CurveType;

/**
 * Calculator for short weierstrass curves with A = -3 mod p
 */
class SW_ANeg3_Calculator extends AbstractCalculator
{
    use JacobiCoordinator;
    use SW_ANeg3_Jacobi_Adder;
    use JacobiSwapper;
    /** @use DoubleAndAddAlwaysMultiplicator<JacobiPoint> */
    use DoubleAndAddAlwaysMultiplicator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        // check allowed to use this calculator
        $check = $curve->getType() === CurveType::ShortWeierstrass;
        $check &= gmp_cmp($curve->getA(), gmp_sub($curve->getP(), 3)) === 0;
        if (!$check) {
            throw new \AssertionError('Cannot use this calculator with the chosen curve.');
        }
    }
}
