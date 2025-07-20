<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\SW_ANeg3_Calculator;
use Famoser\Elliptic\Math\Traits\NativeMathTrait;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\Point;

/**
 * Assumes Short Weierstrass curve with a=-3
 *
 * Some hardening against side-channels has been done.
 */
class SW_ANeg3_Math extends AbstractMath implements MathInterface
{
    use NativeMathTrait;

    private readonly SW_ANeg3_Calculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new SW_ANeg3_Calculator($curve);
    }
}
