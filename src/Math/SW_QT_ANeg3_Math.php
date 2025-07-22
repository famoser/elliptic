<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Traits\TwistedMathTrait;
use Famoser\Elliptic\Math\Twister\QuadraticTwister;
use Famoser\Elliptic\Primitives\Curve;
use Famoser\Elliptic\Primitives\QuadraticTwist;

/**
 * Assumes Short Weierstrass curve which can be transformed by a quadratic twist to a Short Weierstrass curve with a=-3.
 *
 * Some hardening against side-channels has been done.
 */
class SW_QT_ANeg3_Math extends AbstractMath implements MathInterface
{
    use TwistedMathTrait;

    private readonly QuadraticTwister $twister;
    private readonly SW_ANeg3_Math $math;

    public function __construct(Curve $curve, QuadraticTwist $twist)
    {
        parent::__construct($curve);

        $this->twister = new QuadraticTwister($curve, $twist);
        $twistedCurve = $this->twister->twistCurve();

        $this->math = new SW_ANeg3_Math($twistedCurve);
    }
}
