<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\SW_ANeg3_Calculator;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Assumes Short Weierstrass curve with a=-3
 *
 * Some hardening against side-channels has been done.
 */
class SW_ANeg3_Math extends AbstractMath implements MathInterface
{
    private readonly SW_ANeg3_Calculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new SW_ANeg3_Calculator($curve);
    }

    public function add(Point $a, Point $b): Point
    {
        $nativeA = $this->calculator->affineToNative($a);
        $nativeB = $this->calculator->affineToNative($b);

        $nativeResult = $this->calculator->add($nativeA, $nativeB);

        return $this->calculator->nativeToAffine($nativeResult);
    }

    public function double(Point $a): Point
    {
        $nativeA = $this->calculator->affineToNative($a);

        $nativeResult = $this->calculator->double($nativeA);

        return $this->calculator->nativeToAffine($nativeResult);
    }

    public function mul(Point $point, \GMP $factor): Point
    {
        $native = $this->calculator->affineToNative($point);

        $nativeResult = $this->calculator->mul($native, $factor);

        return $this->calculator->nativeToAffine($nativeResult);
    }
}
