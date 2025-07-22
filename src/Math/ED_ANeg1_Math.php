<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\ED_ANeg1_Calculator;
use Famoser\Elliptic\Math\Traits\NativeMathTrait;
use Famoser\Elliptic\Primitives\Curve;

/**
 * Edwards math for a=-1 mod p
 */
class ED_ANeg1_Math extends AbstractMath implements MathInterface
{
    use NativeMathTrait;

    private readonly ED_ANeg1_Calculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new ED_ANeg1_Calculator($curve);
    }
}
