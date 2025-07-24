<?php

namespace Famoser\Elliptic\Math;

use Famoser\Elliptic\Math\Calculator\ED_Calculator;
use Famoser\Elliptic\Math\Traits\NativeMathTrait;
use Famoser\Elliptic\Primitives\Curve;

/**
 * (Untwisted) Edwards math
 */
class ED_Math extends AbstractMath implements MathInterface
{
    use NativeMathTrait;

    private readonly ED_Calculator $calculator;

    public function __construct(Curve $curve)
    {
        parent::__construct($curve);

        $this->calculator = new ED_Calculator($curve);
    }
}
