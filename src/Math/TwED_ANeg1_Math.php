<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\TwED_ANeg1_Calculator;
use Famoser\Elliptic\Math\Traits\NativeMathTrait;
use Famoser\Elliptic\Primitives\Curve;

/**
 * Twisted Edwards math for a=-1 mod p
 */
class TwED_ANeg1_Math extends AbstractMath implements MathInterface
{
    use NativeMathTrait;

    private readonly TwED_ANeg1_Calculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new TwED_ANeg1_Calculator($curve);
    }
}
