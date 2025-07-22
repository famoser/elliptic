<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Traits\BirationalMathTrait;
use Famoser\Elliptic\Primitives\BirationalMap;
use Famoser\Elliptic\Primitives\Curve;

/**
 * Assumes a montgomery curve with a birational map to a twisted edwards curve a=-1.
 *
 * Some hardening against side-channels has been done.
 */
class MG_ED_Math extends AbstractMath implements MathInterface
{
    use BirationalMathTrait;

    private readonly ED_ANeg1_Math $math;

    public function __construct(Curve $curve, private readonly BirationalMap $birationalMap, Curve $targetCurve)
    {
        parent::__construct($curve);

        $this->math = new ED_ANeg1_Math($targetCurve);
    }
}
