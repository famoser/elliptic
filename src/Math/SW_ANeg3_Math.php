<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Algorithm\DoubleAndAddAlways;
use Famoser\Elliptic\Math\Calculator\SW_ANeg3_Jacobi_Affine_Calculator;
use Famoser\Elliptic\Math\Utils\ConstSwapper;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Assumes Short Weierstrass curve with a=-3
 * Hence of the form y^2 = x^3 + ax + b for a = -3 mod p
 */
class SW_ANeg3_Math extends BaseMath implements MathInterface
{
    private readonly DoubleAndAddAlways $doubleAndAddAlways;

    public function __construct(Curve $curve)
    {
        $swapper = new ConstSwapper();
        $calculator = new SW_ANeg3_Jacobi_Affine_Calculator($curve, $swapper);
        parent::__construct($calculator);

        $this->doubleAndAddAlways = new DoubleAndAddAlways($calculator);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        return $this->doubleAndAddAlways->mul($point, $factor);
    }

    public function mulG(\GMP $factor): Point
    {
        /**
         * Optimization potential here:
         * - For window (e.g. of size 4 bits), precompute G into table t. Then, Q <- 2Q; Q <- Q + t[window]
         * - Generalize above method, precompute full table (to also avoid doubling)
         *
         * But needs to be measured whether actually some advantage, as:
         * - Table needs to be fully traversed, else private key imprinted in cache
         * - Especially costly as above implied swapping two values (x,y) repeatedly
         * - Maybe faster by encoding table as string, and only generating chosen gmp afterwards?
         */
        return parent::mulG($factor);
    }
}
